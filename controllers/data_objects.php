<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Data_objects extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_data_objects_model');
		$this->load->helper('storylines');

		Template::set_block('sub_nav', 'custom/_sub_nav');

		$this->lang->load('storylines');
	}

	//--------------------------------------------------------------------
	
    public function index()
    {
		
		$offset = $this->uri->segment(5);

        // Do we have any actions?
        if ($action = $this->input->post('submit'))
        {
            $checked = $this->input->post('checked');

            switch(strtolower($action))
            {
               case 'deactivate':
					$this->change_status($checked, false);
					break;
				case 'delete':
                    $this->delete($checked);
                    break;
            	case 'activate':
				default:	
					$this->change_status($checked, true);
					break;
			}	
        }

        $where = array();

        // Filters
		$filter = $this->input->get('filter');
        switch($filter)
        {
            case 'inactive':
                $where['list_storylines_data_objects.active'] = 0;
                break;
            default:
                $where['list_storylines_data_objects.active'] = 1;
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_data_objects_model->limit($this->limit, $offset)->where($where);
        $this->storylines_data_objects_model->select('id, name, slug, active');

        Template::set('data_objects', $this->storylines_data_objects_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_data_objects_model->where($where);
        $total_stories = $this->storylines_data_objects_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/data_objects/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set_view('storylines/custom/data_objects');
        Template::set('toolbar_title', lang('sl_data_objects'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_data_object())
			{
				$article = $this->storylines_data_objects_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/data_objects');

				Template::set_message('Storyline Data Object successfully created.', 'success');
				Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline data object: '. $this->storylines_data_objects_model->error);
			}
		}
        
		Template::set('toolbar_title', lang('sl_create_data_object'));
		Template::set_view('storylines/custom/data_object_form');
		Template::render();
	}
	
	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		$data_object_id = $this->uri->segment(6);

		if (empty($data_object_id))
		{
			Template::set_message(lang('sl_empty_id'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_data_object('update', $data_object_id))
			{
				$data_object = $this->storylines_data_objects_model->find($data_object_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline Data Object successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the Storyline Data Object: '. $this->storylines_data_objects_model->error);
			}
		}

		$data_object = $this->storylines_data_objects_model->find($data_object_id);
		
		if (isset($data_object))
		{
			Template::set('data_object', $data_object);
		}
		else
		{
			Template::set_message(lang('sl_no_data_object_matches'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}
		
		Template::set('toolbar_title', lang('sl_edit_data_object'));
		Template::set_view('storylines/custom/data_object_form');
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
			$this->auth->restrict('Storylines.Data.Manage');

			foreach ($items as $id)
			{
				$item = $this->storylines_data_objects_model->find($id);

				if (isset($item))
				{
					if ($this->storylines_data_objects_model->delete($id))
					{
						$this->load->model('activities/Activity_model', 'activity_model');

						$item = $this->storylines_data_objects_model->find($id);
						$user = $this->user_model->find($this->current_user->id);
						$log_name = $this->settings_lib->item('auth.use_own_names') ? $this->current_user->username : ($this->settings_lib->item('auth.use_usernames') ? $user->username : $user->email);
						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ': '.$log_name, 'storylines/data_objects');
						Template::set_message('The Storyline Data Object was successfully deleted.', 'success');
					} else {
						Template::set_message(lang('us_action_not_deleted'). $this->storylines_data_objects_model->error, 'error');
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
		redirect(SITE_AREA .'/custom/storylines/data_manage');
	}
	
	//--------------------------------------------------------------------

	public function change_status($items=false, $active = 1)
	{
		if (!$items)
		{
			return;
		}
		$this->auth->restrict('Storylines.Data.Manage');
		
		foreach ($items as $item_id)
		{
			$this->storylines_data_objects_model->update($item_id, array('active' => $active));
		}
	}
	//--------------------------------------------------------------------

	private function save_data_object($type='insert', $id = 0)
	{
		$db_prefix = $this->db->dbprefix;

		$this->form_validation->set_rules('name', lang('sl_name'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('slug', lang('sl_slug'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('description', lang('sl_description'), 'strip_tags|trim|max_length[1000]|xss_clean');
		$this->form_validation->set_rules('active', lang('sl_active'), 'strip_tags|numeric|trim|xss_clean');

		if ($this->form_validation->run() === false)
		{
			return false;
		}
		$data = array(
					'name'=>$this->input->post('name'),
					'slug'=>$this->input->post('slug'),
					'description'=>($this->input->post('description')) ? $this->input->post('description') : ''
		);
		if ($this->input->post('activate')) $data['active'] = 1;
		if ($this->input->post('deactivate')) $data['active'] = 0;
		
		if ($type == 'insert')
		{
			return $this->storylines_data_objects_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_data_objects_model->update($id, $data);
		}
	}
}
// End main module class