<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tokens extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_tokens_model');
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
                $where['list_storylines_tokens.active'] = 0;
                break;
            default:
                $where['list_storylines_tokens.active'] = 1;
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_tokens_model->limit($this->limit, $offset)->where($where);
        $this->storylines_tokens_model->select('id, name, slug, active');

        Template::set('tokens', $this->storylines_tokens_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_tokens_model->where($where);
        $total_stories = $this->storylines_tokens_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/tokens/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set_view('storylines/custom/tokens');
        Template::set('toolbar_title', lang('sl_tokens'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_token())
			{
				$article = $this->storylines_tokens_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/tokens');

				Template::set_message('Storyline Trigger successfully created.', 'success');
				Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline token: '. $this->storylines_tokens_model->error);
			}
		}
        
		Template::set('toolbar_title', lang('sl_create_token'));
		Template::set_view('storylines/custom/token_form');
		Template::render();
	}
	
	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		$token_id = $this->uri->segment(6);

		if (empty($token_id))
		{
			Template::set_message(lang('sl_empty_id'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_token('update', $token_id))
			{
				$token = $this->storylines_tokens_model->find($token_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline Trigger successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the Storyline Trigger: '. $this->storylines_tokens_model->error);
			}
		}

		$token = $this->storylines_tokens_model->find($token_id);
		
		if (isset($token))
		{
			Template::set('token', $token);
		}
		else
		{
			Template::set_message(lang('sl_no_token_matches'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}
		
		Template::set('toolbar_title', lang('sl_edit_token'));
		Template::set_view('storylines/custom/token_form');
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
				$item = $this->storylines_tokens_model->find($id);

				if (isset($item))
				{
					if ($this->storylines_tokens_model->delete($id))
					{
						$this->load->model('activities/Activity_model', 'activity_model');

						$item = $this->storylines_tokens_model->find($id);
						$user = $this->user_model->find($this->current_user->id);
						$log_name = $this->settings_lib->item('auth.use_own_names') ? $this->current_user->username : ($this->settings_lib->item('auth.use_usernames') ? $user->username : $user->email);
						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ': '.$log_name, 'storylines/tokens');
						Template::set_message('The Storyline Data Object was successfully deleted.', 'success');
					} else {
						Template::set_message(lang('us_action_not_deleted'). $this->storylines_tokens_model->error, 'error');
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
			$this->storylines_tokens_model->update($item_id, array('active' => $active));
		}
	}
	//--------------------------------------------------------------------

	private function save_token($type='insert', $id = 0)
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
			return $this->storylines_tokens_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_tokens_model->update($id, $data);
		}
	}
}
// End main module class