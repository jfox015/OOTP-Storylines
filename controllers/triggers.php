<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2012 Jeff Fox

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/
class Triggers extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_triggers_model');
		$this->load->helper('storylines');

		Template::set_block('sub_nav', 'custom/_sub_nav');

		$this->lang->load('storylines');
	}

	//--------------------------------------------------------------------
	
    public function index()
    {
		
		$offset = $this->uri->segment(6);

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
                $where['list_storylines_triggers.active'] = 0;
                break;
            default:
                $where['list_storylines_triggers.active'] = 1;
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_triggers_model->limit($this->limit, $offset)->where($where);
        $this->storylines_triggers_model->select('id, name, slug, active');

        Template::set('triggers', $this->storylines_triggers_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_triggers_model->where($where);
        $total_stories = $this->storylines_triggers_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/triggers/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set_view('storylines/custom/triggers');
        Template::set('toolbar_title', lang('sl_triggers'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_trigger())
			{
				$article = $this->storylines_triggers_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/triggers');

				Template::set_message('Storyline Trigger successfully created.', 'success');
				Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline trigger: '. $this->storylines_triggers_model->error);
			}
		}
        
		Template::set('toolbar_title', lang('sl_create_trigger'));
		Template::set_view('storylines/custom/trigger_form');
		Template::render();
	}
	
	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		$trigger_id = $this->uri->segment(6);

		if (empty($trigger_id))
		{
			Template::set_message(lang('sl_empty_id'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_trigger('update', $trigger_id))
			{
				$trigger = $this->storylines_triggers_model->find($trigger_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline Trigger successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the Storyline Trigger: '. $this->storylines_triggers_model->error);
			}
		}

		$trigger = $this->storylines_triggers_model->find($trigger_id);
		
		if (isset($trigger))
		{
			Template::set('trigger', $trigger);
		}
		else
		{
			Template::set_message(lang('sl_no_trigger_matches'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}
		
		Template::set('toolbar_title', lang('sl_edit_trigger'));
		Template::set_view('storylines/custom/trigger_form');
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
				$item = $this->storylines_triggers_model->find($id);

				if (isset($item))
				{
					if ($this->storylines_triggers_model->delete($id))
					{
						$this->load->model('activities/Activity_model', 'activity_model');

						$item = $this->storylines_triggers_model->find($id);
						$user = $this->user_model->find($this->current_user->id);
						$log_name = $this->settings_lib->item('auth.use_own_names') ? $this->current_user->username : ($this->settings_lib->item('auth.use_usernames') ? $user->username : $user->email);
						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ': '.$log_name, 'storylines/triggers');
						Template::set_message('The Storyline Data Object was successfully deleted.', 'success');
					} else {
						Template::set_message(lang('us_action_not_deleted'). $this->storylines_triggers_model->error, 'error');
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

	private function change_status($items=false, $active = 1)
	{
		if (!$items)
		{
			return;
		}
		$this->auth->restrict('Storylines.Data.Manage');
		
		foreach ($items as $item_id)
		{
			$this->storylines_triggers_model->update($item_id, array('active' => $active));
		}
	}
	//--------------------------------------------------------------------

	private function save_trigger($type='insert', $id = 0)
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
			return $this->storylines_triggers_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_triggers_model->update($id, $data);
		}
	}
}
// End main module class