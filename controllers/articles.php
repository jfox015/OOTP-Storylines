<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Articles extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_articles_model');
		$this->load->model('storylines_category_model');
		$this->load->model('storylines_status_model');
	}

	//--------------------------------------------------------------------
	
    public function index()
    {

        $categories = $this->storylines_category_model->select('id, name')->find_all();
        Template::set('categories', $categories);
		
		$statuses = $this->storylines_status_model->select('id, name')->find_all();
        Template::set('statuses', $statuses);
		
		$this->load->model('author_model');
		$users = $this->author_model->get_users_select();
		Template::set('users', $users);
		
        $offset = $this->uri->segment(5);

        // Do we have any actions?
        if ($action = $this->input->post('submit'))
        {
            $checked = $this->input->post('checked');

            switch(strtolower($action))
            {
                case 'approve':
                    $this->approve($checked);
                    break;
                case 'in review':
                    $this->review($checked);
                    break;
                case 'reject':
                    $this->reject($checked);
                    break;
                case 'archive':
                    $this->archive($checked);
                    break;
                case 'delete':
                    $this->delete($checked);
                    break;
            }
        }

        $where = array();

        // Filters
		$filter = $this->input->get('filter');
        switch($filter)
        {
            case 'category':
                $category_id = (int)$this->input->get('category_id');
                $where['storylines.category_id'] = $category_id;
                foreach ($categories as $category)
                {
                    if ($category->id == $category_id)
                    {
                        Template::set('filter_category', $category->name);
                        break;
                    }
                }
                break;
            case 'added':
                $where['storylines.status_id'] = 1;
                break;
            case 'review':
				$where['storylines.status_id'] = 2;
                break;
            case 'rejected':
				$where['storylines.status_id'] = 4;
                break;
            case 'archived':
				$where['storylines.status_id'] = 5;
                break;
            case 'deleted':
                $where['storylines.deleted'] = 1;
                break;
            case 'author':
                $author_id = (int)$this->input->get('author_id');
                $where['storylines.created_by'] = $author_id;
                foreach ($users as $user)
                {
                    if ($user->user_id == $author_id)
                    {
                        Template::set('filter_author', $user->username);
                        break;
                    }
                }
                break;
            default:
                $where['storylines.category_id'] = 1;
                $this->user_model->where('storylines.deleted', 0);
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_model->join('list_storylines_categories','list_storylines_categories.id = storylines.category_id');
        $this->storylines_model->join('list_storylines_status','list_storylines_status.id = storylines.status_id');
        $this->storylines_model->limit($this->limit, $offset)->where($where);
        $this->storylines_model->select('storylines.id, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.status_id, list_storylines_status.name as status_name, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');

        Template::set('storylines', $this->storylines_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_model->where($where);
        $total_stories = $this->storylines_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set('toolbar_title', lang('sl_custom_header'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Stories.Add');

		if ($this->input->post('submit'))
		{
			if ($id = $this->save_storyline())
			{
				$storyline = $this->storylines_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline successfully created.', 'success');
				$dest = '/custom/stoylines/';
				if ($this->input->post('edit_after_create')) {
					$dest = '/custom/stoylines/edit/'.$id;
				}
				Template::redirect(SITE_AREA .$dest);	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline: '. $this->storylines_model->error);
			}
		}
        Template::set('categories', $this->storylines_category_model->select('id, name')->find_all());

		Template::set('toolbar_title', lang('sl_create_storyline'));
		Template::set_view('storylines/custom/create_form');
		Template::render();
	}
	

	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Stories.Manage');
		
		$storyline_id = $this->uri->segment(5);
		if (empty($storyline_id))
		{
			Template::set_message(lang('us_empty_id'), 'error');
			redirect(SITE_AREA .'/custom/storylines');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_storyline('update', $storyline_id))
			{
				$storyline = $this->storylines_model->find($storyline_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the storyline: '. $this->storylines_model->error);
			}
		}

		$storyline = $this->storylines_model->find($article_id);
		if (isset($storyline))
		{
			Template::set('storyline', $storyline);
			Template::set('categories', $this->storylines_category_model->select('id, name')->find_all());
			Template::set('statuses', $this->storylines_status_model->select('id, name')->find_all());
			Template::set_view('storylines/custom/edit_form');
		}
		else
		{
			Template::set_message(sprintf(lang('us_unauthorized')), 'error');
			redirect(SITE_AREA .'/custom/storylines');
		}

		Template::set('toolbar_title', lang('sl_edit_storyline'));
		Template::render();
	}

	//--------------------------------------------------------------------

	private function save_storyline($type='insert', $id=0)
	{
		$db_prefix = $this->db->dbprefix;

		if ($type == 'insert')
		{
			$this->form_validation->set_rules('title', lang('sl_title'), 'required|trim|max_length[255]|xss_clean');
			$this->form_validation->set_rules('description', lang('sl_description'), 'required|trim|xss_clean');
			$this->form_validation->set_rules('category_id', lang('sl_category'), 'required|trim|number|max_length[2]|xss_clean');
		}
		else
		{
			$this->form_validation->set_rules('title', lang('sl_title'), 'required|trim|max_length[255]|xss_clean');
			$this->form_validation->set_rules('description', lang('sl_description'), 'required|trim|xss_clean');
			$this->form_validation->set_rules('category_id', lang('sl_category'), 'required|trim|number|max_length[2]|xss_clean');
		}

		if ($this->form_validation->run() === false)
		{
			return false;
		}
		$data = array(
					'title'=>$this->input->post('title'),
					'description'=>$this->input->post('description'),
					'category_id'=>$this->input->post('category_id')
		);

		if ($type == 'insert')
		{
			return $this->storylines_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_model->update($id, $data);
		}
	}
}
// End main module class