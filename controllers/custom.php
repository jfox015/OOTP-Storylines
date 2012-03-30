<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Custom extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_category_model');
		$this->load->model('storylines_review_status_model');
		$this->load->model('storylines_publish_status_model');
		$this->load->helper('storylines');

		Template::set_block('sub_nav', 'custom/_sub_nav');

		$this->lang->load('storylines');
	}

	//--------------------------------------------------------------------
	
    public function index()
    {

        $categories = $this->storylines_category_model->select('id, name')->find_all();
        Template::set('categories', $categories);
		
		$publish_statuses = $this->storylines_publish_status_model->select('id, name')->find_all();
        Template::set('publish_statuses', $publish_statuses);
		
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
                    $this->change_status($checked, 3);
                    break;
                case 'in review':
                    $this->change_status($checked, 2);
                    break;
                case 'reject':
                    $this->change_status($checked, 4);
                    break;
                case 'archive':
                    $this->change_status($checked, 5);
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
                $where['storylines.publish_status_id'] = 1;
                break;
            case 'review':
				$where['storylines.publish_status_id'] = 2;
                break;
            case 'rejected':
				$where['storylines.publish_status_id'] = 4;
                break;
            case 'archived':
				$where['storylines.publish_status_id'] = 5;
                break;
            case 'deleted':
                $where['storylines.deleted'] = 1;
                break;
            case 'author':
                $author_id = (int)$this->input->get('author_id');
                $where['storylines.created_by'] = $author_id;
                foreach ($users as $user_id => $display_name)
                {
                    if ($user_id == $author_id)
                    {
                        Template::set('filter_author', $display_name);
                        break;
                    }
                }
                break;
            default:
                $where['storylines.publish_status_id'] = 3;
                $this->user_model->where('storylines.deleted', 0);
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;

		$this->limit($this->limit, $offset)->where($where);
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

		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set('toolbar_title', lang('sl_custom_header'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Content.Add');

		if ($this->input->post('submit'))
		{
			if ($id = $this->save_storyline())
			{
				$storyline = $this->storylines_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline successfully created.', 'success');
				$dest = '/custom/storylines/';
				if ($this->input->post('edit_after_create')) {
					$dest = '/custom/storylines/edit/'.$id;
				}
				Template::redirect(SITE_AREA .$dest);	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline: '. $this->storylines_model->error);
			}
		}
		Template::set('categories', $this->storylines_category_model->list_as_select());

		Template::set('toolbar_title', lang('sl_create_storyline'));
		Template::set_view('storylines/custom/create_form');
		Template::render();
	}
	

	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Content.Manage');
		
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

		$storyline = $this->storylines_model->find($storyline_id);
		if (isset($storyline))
		{
			Template::set('storyline', $storyline);
			if (!isset($this->storylines_articles_model)) {
				$this->load->model('storylines_articles_model');
			}
			if (!isset($this->storylines_data_objects_model)) {
				$this->load->model('storylines_data_objects_model');
			}
			Template::set('characters', $this->storylines_data_objects_model->find_all_by('storyline_id',$storyline_id));
			Template::set('articles', $this->storylines_articles_model->build_article_tree($storyline_id));
			Template::set('categories', $this->storylines_category_model->list_as_select());
			Template::set('publish_statuses', $this->storylines_publish_status_model->list_as_select());
			Template::set('review_statuses', $this->storylines_review_status_model->list_as_select());
			$comments = (in_array('comments',module_list(true))) ? modules::run('comments/thread_view_with_form',$storyline->comments_thread_id) : '';
			Template::set('comment_form', $comments);
			Assets::add_js($this->load->view('storylines/custom/edit_form_js',array('storyline'=>$storyline),true),'inline');
			Template::set_view('storylines/custom/edit_form');
		}
		else
		{
			Template::set_message(lang('sl_no_storyline_found'), 'error');
			redirect(SITE_AREA .'/custom/storylines');
		}

		Template::set('toolbar_title', lang('sl_edit_storyline'));
		Template::render();
	}

	//--------------------------------------------------------------------

	public function delete($items)
	{
		if (empty($items))
		{
			$item_id = $this->uri->segment(5);

			if(!empty($item_id))
			{
					$items = array($item_id);
			}
		}

		if (!empty($items))
		{
			$this->auth->restrict('Storylines.Content.Manage');

			foreach ($items as $id)
			{
				$item = $this->storylines_model->find($id);

				if (isset($item))
				{
					if ($this->storylines_model->delete($id))
					{
						$this->load->model('activities/Activity_model', 'activity_model');

						$item = $this->storylines_model->find($id);
						$user = $this->user_model->find($this->current_user->id);
						$log_name = $this->settings_lib->item('auth.use_own_names') ? $this->current_user->username : ($this->settings_lib->item('auth.use_usernames') ? $user->username : $user->email);
						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ': '.$log_name, 'storylines');
						Template::set_message('The Storyline was successfully deleted.', 'success');
					} else {
						Template::set_message(lang('us_action_not_deleted'). $this->storylines_model->error, 'error');
					}
				}
				else 
				{
					Template::set_message(lang('sl_no_matches_found'), 'error');
				}
			}
		}
		else 
		{
				Template::set_message(lang('us_empty_id'), 'error');
		}
		redirect(SITE_AREA .'/custom/storylines');
	}
	//--------------------------------------------------------------------

	public function change_status($items=false, $status_id = 1)
	{
		if (!$items)
		{
			return;
		}
		$this->auth->restrict('Storylines.Content.Manage');
		
		foreach ($items as $item_id)
		{
			$this->storylines_model->update($item_id, array('publish_status' => $status_id));
		}
	}

	//--------------------------------------------------------------------

	private function save_storyline($type='insert', $id = 0)
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
					'category_id'=>($this->input->post('category_id') ? $this->input->post('category_id') : 1),
					'publish_status_id'=>($this->input->post('publish_status_id') ? $this->input->post('publish_status_id') : 1),
					'review_status_id'=>($this->input->post('review_status_id') ? $this->input->post('review_status_id') : 1),
					'tags'=>($this->input->post('tags') ? $this->input->post('tags') : ''),
					'modified_by'=>$this->current_user->id
		);

		if ($type == 'insert')
		{
			$thread_id = 0;
			if (in_array('comments',module_list(true))) 
			{
				if(!isset($this->comments_model)) 
				{
					$this->load->model('comments/comments_model');
				}
				$thread_id = $this->comments_model->new_comments_thread();
			}
			$data = $data + array('comments_thread_id'=>$thread_id,'created_by'=>$this->current_user->id);
			return $this->storylines_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_model->update($id, $data);
		}
	}
}
// End main module class