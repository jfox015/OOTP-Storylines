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

class Custom extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_category_model');
		$this->load->model('storylines_author_status_model');
		$this->load->model('storylines_publish_status_model');
		$this->load->model('storylines_history_model');
		$this->load->helper('storylines');

		Template::set_block('sub_nav', 'custom/_sub_nav');

		$this->lang->load('storylines');
	}
	
	//--------------------------------------------------------------------
	//	!STORYLINES SPECIFIC FUNCTIONS
	//--------------------------------------------------------------------

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

		$this->storylines_model->limit($this->limit, $offset)->where($where);
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
			
			$this->load->model('storylines_articles_model');
			$this->load->model('storylines_conditions_model');
			$this->load->model('storylines_data_objects_model');
			$this->load->model('storylines_random_frequencies_model');
			$this->load->model('storylines_category_model');
			$this->load->model('storylines_triggers_model');
			
			// Storyline Data sets
			Template::set('conditions', $this->storylines_conditions_model->get_object_conditions($storyline_id, 1));
			Template::set('data_objects', $this->storylines_model->get_data_objects($storyline_id));
			Template::set('articles', $this->storylines_articles_model->build_article_tree($storyline_id));
			Template::set('triggers', $this->storylines_model->get_triggers($storyline_id));
			Template::set('unique_status', $this->storylines_model->get_unique_status($storyline_id));

			// Options Lists Data
			//Template::set('conditions_objs', $this->storylines_conditions_model->list_as_select_by_category());
			Template::set('triggers_list', $this->storylines_triggers_model->list_as_select());
			Template::set('characters_list', $this->storylines_data_objects_model->list_as_select());
			Template::set('frequencies', $this->storylines_random_frequencies_model->list_as_select());
			Template::set('categories', $this->storylines_category_model->list_as_select());
			Template::set('publish_statuses', $this->storylines_publish_status_model->list_as_select());
			Template::set('author_statuses', $this->storylines_author_status_model->list_as_select());
			
			// COMMENTS
			$comments = (in_array('comments',module_list(true))) ? modules::run('comments/thread_view_with_form',$storyline->comments_thread_id) : '';
			Template::set('comment_form', $comments);
			Template::set_theme('admin');
			// ADD Conditions supportiog JS and CSS
			Assets::add_js($this->load->view('storylines/custom/utility_js',null,true),'inline');
			Assets::add_js($this->load->view('storylines/custom/conditions_js',null,true),'inline');
			Assets::add_js($this->load->view('storylines/custom/edit_form_js',array('storyline'=>$storyline),true),'inline');
			Assets::add_js(array(js_path() . 'json2.js',Template::theme_url('js/jquery-ui-1.8.13.min.js')));
			Assets::add_css(Template::theme_url('css/flick/jquery-ui-1.8.13.custom.css'));

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

						//$item = $this->storylines_model->find($id);
						$user = $this->user_model->find($this->current_user->id);
						$log_name = $this->settings_lib->item('auth.use_own_names') ? $this->current_user->username : ($this->settings_lib->item('auth.use_usernames') ? $user->username : $user->email);
						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ': '.$log_name, 'storylines');
						Template::set_message('The Storyline was successfully deleted.', 'success');
						
						if (in_array('comments',module_list(true))) {
							modules::run('comments/purge_thread',$item->comments_thread_id);
						}
			
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
	//	!ADDITIONAL FUNCTIONAL PAGES
	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------
	public function export_selected($checked = false, $format = 'xml')
	{
		if (!$checked)
		{
			return;
		}
		$storylnes = $this->storylines_model->get_complete_storylines($checked, false, 3);
		export_storylines($format, $storylnes);
	}
	public function export()
	{
		
		$format = $this->uri->segment(5);
		$statuses = $this->uri->segment(6);
		$statuses = (isset($statuses) ? $statuses : 3);
		$dataOut = array();
		
		if ((isset($format) && !empty($format)))
		{
			if (strpos($statuses,"|") !== false) $statuses = explode("|",$statuses);
			$storylnes = $this->storylines_model->get_complete_storylines(false, false, $statuses);
			$dataOut = export_storylines($format, $storylnes);
		}	
		if (isset($dataOut['header']) && !empty($dataOut['header']))
		{
			$this->output->set_header("Cache-Control: no-cache");
			$this->output->set_header("Pragma: no-cache");
			$this->output->set_header('Content-Disposition: attachment; filename=storylines.'.$format);
			$this->output->set_header($dataOut['header']);
			$this->output->set_output($dataOut['output']);
		}
		else
		{
			if (isset($dataOut['output']) && !empty($dataOut['output']))
			{
				Template::set_message($dataOut['output'],$dataOut['status']);
			}
			Template::set('toolbar_title', lang('sl_export'));
			Template::render();
		}
	}
	
	//--------------------------------------------------------------------
	
	public function manage_data()
	{
		Template::set('toolbar_title', lang('sl_manage_data'));
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	public function reference()
	{
		Template::set('toolbar_title', lang('sl_reference'));
		Template::render();
	}
	
	//--------------------------------------------------------------------
	
	public function history()
	{
		$type_id = $this->uri->segment(5);
		$var_id = $this->uri->segment(6);
		$offset = $this->uri->segment(7);

		if (isset($type_id) && !empty($type_id))
		{
			if (isset($var_id) && !empty($var_id))
			{
				$where = array();
				$where['object_type'] = $type_id;
				$where['var_id'] = $var_id;
				$this->storylines_model->limit($this->limit, $offset)->where($where);
				$history = $this->storylines_history_model->find_all();
				Template::set('history', $history);
				Template::set('type_id', $type_id);
				Template::set('var_id', $var_id);

				// Pagination
				$this->load->library('pagination');

				$this->storylines_history_model->where($where);
				$total_items = $this->storylines_history_model->count_all();


				$this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/history');
				$this->pager['total_rows'] = $total_items;
				$this->pager['per_page'] = $this->limit;
				$this->pager['uri_segment']	= 7;

				$this->pagination->initialize($this->pager);
				Template::set('current_url', current_url());
			}
			else
			{
				Template::set_message(lang('sl_history_empty_var_id'), 'error');
			}
		} 
		else
		{
			Template::set_message(lang('sl_history_empty_type_id'), 'error');
		}
		Template::set('toolbar_title', lang('sl_history'));
		Template::render();
	}
	
	//--------------------------------------------------------------------
	//	!AJAX FUNCTIONS
	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------
	/*
		Method:
			add_data_object()
		
		An ajax method to add a data object to a storyline
		
		Return:
			JSON data object
	*/
	public function add_data_object()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		if ($this->input->post('object_data'))
		{
			$items = json_decode($this->input->post('object_data'));
			$data = array('storyline_id'		=> $items->storyline_id,
						  'object_id'	 		=> $items->object_id
			);
			$this->storylines_model->add_data_object($data);
			$json_out['result']['items'] = $this->storylines_model->get_data_objects($items->storyline_id);
		}
		else
		{
			$error = true;
			$status = "Post data was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	
	//--------------------------------------------------------------------
	/*
		Method:
			get_data_objects()
		
		An ajax method to get a JSON object of data objects for a storyline
		
		Return:
			JSON data object
	*/
	public function get_data_objects()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		$storyline_id = $this->uri->segment(5);
		
		if (isset($storyline_id) && !empty($storyline_id)) 
		{
			$json_out['result']['items'] = $this->storylines_model->get_data_objects($storyline_id);
		}
		else
		{
			$error = true;
			$status = "Storyline ID was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}	
	//--------------------------------------------------------------------
	/*
		Method:
			get_data_objects()
		
		An ajax method to get a JSON object of data objects for a storyline as a select friendly array
		
		Return:
			JSON data object
	*/
	public function get_data_objects_list()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		$storyline_id = $this->uri->segment(5);
		
		if (isset($storyline_id) && !empty($storyline_id)) 
		{
			$json_out['result']['items'] = $this->storylines_model->get_data_objects_list($storyline_id);
		}
		else
		{
			$error = true;
			$status = "Storyline ID was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	//--------------------------------------------------------------------
	/*
		Method:
			remove_data_object()
		
		An ajax method to remove a data object from a storyline
		
		Return:
			JSON data object
	*/
	public function remove_data_object()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		if ($this->input->post('object_data'))
		{
			$items = json_decode($this->input->post('object_data'));
			$this->storylines_model->remove_data_object($items->object_id);
			$json_out['result']['items'] = $this->storylines_model->get_data_objects($items->storyline_id);
		}
		else
		{
			$error = true;
			$status = "Post data was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	
	//--------------------------------------------------------------------
	/*
		Method:
			add_trigger()
		
		An ajax method to add a trigger to a storyline
		
		Return:
			JSON data object
	*/
	public function add_trigger()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		if ($this->input->post('object_data'))
		{
			$items = json_decode($this->input->post('object_data'));
			$data = array('storyline_id'		=> $items->storyline_id,
						  'trigger_id'	 		=> $items->object_id
			);
			$this->storylines_model->add_trigger($data);
			$json_out['result']['items'] = $this->storylines_model->get_triggers($items->storyline_id);
		}
		else
		{
			$error = true;
			$status = "Post data was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	
	//--------------------------------------------------------------------
	/*
		Method:
			get_triggers()
		
		An ajax method to get a JSON object of triggers for a storyline
		
		Return:
			JSON data object
	*/
	public function get_triggers()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		$storyline_id = $this->uri->segment(5);
		
		if (isset($storyline_id) && !empty($storyline_id)) 
		{
			$json_out['result']['items'] = $this->storylines_model->get_triggers($storyline_id);
		}
		else
		{
			$error = true;
			$status = "Storyline ID was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	//--------------------------------------------------------------------
	/*
		Method:
			remove_trigger()
		
		An ajax method to remove a trigger from a storyline
		
		Return:
			JSON data object
	*/
	public function remove_trigger()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		if ($this->input->post('object_data'))
		{
			$items = json_decode($this->input->post('object_data'));
			$this->storylines_model->remove_trigger($items->object_id);
			$json_out['result']['items'] = $this->storylines_model->get_triggers($items->storyline_id);
		}
		else
		{
			$error = true;
			$status = "Post data was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));
	}
	//--------------------------------------------------------------------
	//	!PRIVATE FUNCTIONS
	//--------------------------------------------------------------------

	//--------------------------------------------------------------------

	private function change_status($items=false, $status_id = 1)
	{
		if (!$items)
		{
			return;
		}
		$this->auth->restrict('Storylines.Content.Manage');
		
		foreach ($items as $item_id)
		{
			$this->storylines_model->update($item_id, array('publish_status_id' => $status_id));
		}
	}
	
	//--------------------------------------------------------------------

	private function save_storyline($type='insert', $id = 0)
	{
		$db_prefix = $this->db->dbprefix;

		if ($type == 'update')
		{
		
		}
		$this->form_validation->set_rules('title', lang('sl_title'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('description', lang('sl_description'), 'required|trim|xss_clean');
		$this->form_validation->set_rules('category_id', lang('sl_category'), 'required|trim|numeric|max_length[3]|xss_clean');
		$this->form_validation->set_rules('tags', lang('sl_tags'), 'trim|max_length[255]|xss_clean');

		if ($this->form_validation->run() === false)
		{
			return false;
		}
		$data = array(
					'title'=>$this->input->post('title'),
					'description'=>$this->input->post('description'),
					'category_id'=>($this->input->post('category_id') ? $this->input->post('category_id') : 1),
					'tags'=>($this->input->post('tags') ? $this->input->post('tags') : ''),
					'publish_status_id'=>($this->input->post('publish_status_id') ? $this->input->post('publish_status_id') : 1),
					'author_status_id'=>($this->input->post('author_status_id') ? $this->input->post('author_status_id') : 1),
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