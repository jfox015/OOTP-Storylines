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
		
		$this->load->model('news/author_model');
		$users = $this->author_model->get_users_select();
		Template::set('users', $users);
		
        $storyline_id = $this->uri->segment(5);
		$offset = $this->uri->segment(6);

        // Do we have any actions?
        if ($action = $this->input->post('submit'))
        {
            $checked = $this->input->post('checked');

            switch(strtolower($action))
            {
                case 'delete':
				default:
                    $this->delete($checked);
                    break;
            }
        }

        $where = array();

        // Filters
		$filter = $this->input->get('filter');
        switch($filter)
        {
            case 'deleted':
                $where['storylines_articles.deleted'] = 1;
                break;
            case 'author':
                $author_id = (int)$this->input->get('author_id');
                $where['storylines_articles.created_by'] = $author_id;
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
                $where['storylines_articles.category_id'] = 1;
                $this->user_model->where('storylines.deleted', 0);
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_articles_model->limit($this->limit, $offset)->where($where);
        $this->storylines_articles_model->select('storylines_articles_model.id, sub, created_by, created_on, modified_on, modified_by');

        Template::set('storylines', $this->storylines_articles_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_articles_model->where($where);
        $total_stories = $this->storylines_articles_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/articles');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set('toolbar_title', lang('sl_articles'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Stories.Add');

		$storyline_id = $this->uri->segment(6)
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_article())
			{
				$article = $this->storylines_articles_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/articles');

				Template::set_message('Storyline Article successfully created.', 'success');
				$dest = '/custom/stoylines/';
				if ($this->input->post('edit_after_create')) {
					$dest = '/custom/stoylines/articles/edit/'.$id;
				}
				Template::redirect(SITE_AREA .$dest);	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline: '. $this->storylines_articles_model->error);
			}
		}
        
		Template::set('storyline_id', $storyline_id);
		Template::set('toolbar_title', lang('sl_create_article'));
		Template::set_view('storylines/custom/create_article_form');
		Template::render();
	}
	

	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Stories.Manage');
		
		$article_id = $this->uri->segment(5);
		if (empty($article_id))
		{
			Template::set_message(lang('us_empty_id'), 'error');
			redirect(SITE_AREA .'/custom/storylines');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_article('update', $article_id))
			{
				$article = $this->storylines_articles_model->find($article_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline Article successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the storyline Article: '. $this->storylines_articles_model->error);
			}
		}

		$storyline = $this->storylines_articles_model->find($article_id);
		if (isset($storyline))
		{
			Template::set('storyline', $storyline);
			Template::set_view('storylines/custom/articles/edit_article_form');
		}
		else
		{
			Template::set_message(sprintf(lang('us_unauthorized')), 'error');
			redirect(SITE_AREA .'/custom/storylines/'.$storyline_id);
		}

		Template::set('toolbar_title', lang('sl_edit_article'));
		Template::render();
	}

	//--------------------------------------------------------------------

	private function save_article($type='insert', $id=0)
	{
		$db_prefix = $this->db->dbprefix;

		if ($type == 'insert')
		{
			$this->form_validation->set_rules('subject', lang('sl_title'), 'required|trim|max_length[255]|xss_clean');
			$this->form_validation->set_rules('text', lang('sl_description'), 'required|trim|xss_clean');
		}
		else
		{
			$this->form_validation->set_rules('subject', lang('sl_title'), 'required|trim|max_length[255]|xss_clean');
			$this->form_validation->set_rules('text', lang('sl_description'), 'required|trim|xss_clean');
		}

		if ($this->form_validation->run() === false)
		{
			return false;
		}
		$data = array(
					'title'=>$this->input->post('subject'),
					'description'=>$this->input->post('text')
		);

		if ($type == 'insert')
		{
			$data = $data + array('storyline_id'=>$this->input->post('$storyline_id'));
			return $this->storylines_articles_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_articles_model->update($id, $data);
		}
	}
}
// End main module class