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
class Articles extends Admin_Controller {

	var $save_error = null;
	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_articles_model');
		$this->load->model('storylines_tokens_model');
		$this->load->helper('storylines');

		Template::set_block('sub_nav', 'custom/_sub_nav');

		$this->lang->load('storylines');
	}

	//--------------------------------------------------------------------
	
    public function index()
    {
		
		$this->load->model('news/author_model');
		$users = $this->author_model->get_users_select();
		Template::set('users', $users);
		
        $storyline_id = $this->uri->segment(6);
		$offset = $this->uri->segment(7);

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
                $this->user_model->where('storylines_articles.deleted', 0);
                break;
        }
		$where['storylines_articles.storyline_id'] = $storyline_id;
        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_articles_model->limit($this->limit, $offset)->where($where);
        $this->storylines_articles_model->select('storylines_articles.id, title, subject, created_by, created_on, modified_on, modified_by,
												(SELECT COUNT('.$dbprefix.'storylines_conditions.id) FROM '.$dbprefix.'storylines_conditions WHERE '.$dbprefix.'storylines_conditions.var_id = '.$dbprefix.'storylines_articles.id AND level_type = 2) as condition_count,
												(SELECT COUNT('.$dbprefix.'storylines_article_results.id) FROM '.$dbprefix.'storylines_article_results WHERE '.$dbprefix.'storylines_article_results.article_id = '.$dbprefix.'storylines_articles.id) as result_count');

        Template::set('articles', $this->storylines_articles_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_articles_model->where($where);
        $total_articles = $this->storylines_articles_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/articles');
        $this->pager['total_rows'] = $total_articles;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 7;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set_view('storylines/custom/articles');
        Template::set('toolbar_title', lang('sl_articles'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Content.Add');

		$storyline_id = $this->uri->segment(6);
		$predecessor_id = $this->uri->segment(7);

		if ($this->input->post('submit'))
		{
			if ($id = $this->save_article())
			{
				$article = $this->storylines_articles_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/articles');

				Template::set_message('Storyline Article successfully created.', 'success');
				$dest = '/custom/storylines/edit/'.$storyline_id;
				if ($this->input->post('edit_after_create')) {
					$dest = '/custom/storylines/articles/edit/'.$id;
				}
				Template::redirect(SITE_AREA .$dest);	
			}
			else
			{
				if (!empty($this->storylines_articles_model->error))
				{
					$this->save_error = $this->storylines_articles_model->error;
				}
				Template::set_message('There was a problem adding the Storyline Article: '.$this->save_error, 'error' );
			}
		}
        
		Template::set('game_message_types', $this->storylines_articles_model->get_game_message_types());
		Template::set('storyline_id', $storyline_id);
		if (isset($predecessor_id) && !empty($predecessor_id))
		{
			Template::set('predecessor_id',$predecessor_id);
		}
		Template::set('toolbar_title', lang('sl_create_article'));
		Template::set_view('storylines/custom/create_article_form');
		Template::render();
	}
	
	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Content.Manage');
		
		$article_id = $this->uri->segment(6);

		if (empty($article_id))
		{
			Template::set_message(lang('sl_empty_id'), 'error');
			redirect(SITE_AREA .'/custom/storylines/');
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
				if (!empty($this->storylines_articles_model->error))
				{
					$this->save_error = $this->storylines_articles_model->error;
				}
				Template::set_message('There was a problem updating the Storyline Article: '.$this->save_error, 'error' );
			}
		}

		$article = $this->storylines_articles_model->find($article_id);
		
		if (isset($article))
		{
			$this->load->model('storylines_conditions_model');
			$this->load->model('storylines_results_model');
			$this->load->model('storylines_data_objects_model');

			
			Template::set('article', $article);
			
			Template::set('characters', $this->storylines_model->get_data_objects($article->storyline_id));
			Template::set('game_message_types', $this->storylines_articles_model->get_game_message_types());
			
			Template::set('storyline', $this->storylines_model->find($article->storyline_id));
			Template::set('comment_form', (in_array('comments',module_list(true))) ? modules::run('comments/thread_view_with_form',$article->comments_thread_id) : '');
			
			Template::set('article_conditions', $this->storylines_articles_model->get_article_conditions($article_id));
			Template::set('article_results', $this->storylines_articles_model->get_article_results_for_form($article_id));
			Template::set('article_predecessor_ids', $this->storylines_articles_model->get_article_predecessor_ids($article_id));

			Template::set('all_articles', $this->storylines_articles_model->get_all_articles($article->storyline_id,$article->id,false));
			
			Template::set_theme('admin');
			
			// ADD Conditions supportiog JS and CSS
            Assets::add_js(base_url().'/assets/js/underscore-min.js','external');
            Assets::add_js($this->load->view('storylines/custom/utility_js',null,true),'inline');
			Assets::add_js($this->load->view('storylines/custom/conditions_js',null,true),'inline');
			Assets::add_js($this->load->view('storylines/custom/edit_article_form_js',array('article'=>$article),true),'inline');
			Assets::add_js(array(js_path() . 'json2.js',Template::theme_url('js/jquery-ui-1.8.13.min.js')));
			Assets::add_css(Template::theme_url('css/flick/jquery-ui-1.8.13.custom.css'));

			Template::set_view('storylines/custom/articles/edit_article_form');

		}
		else
		{
			Template::set_message(lang('sl_no_article_matches'), 'error');
			redirect(SITE_AREA .'/custom/storylines/');
		}
		
		Template::set('toolbar_title', lang('sl_edit_article'));
		Template::set_view('storylines/custom/edit_article_form');
		Template::render();
	}

	//--------------------------------------------------------------------

	public function delete($items)
	{
		$storyline_id = -1;
		if (empty($items))
		{
			$item_id = $this->uri->segment(6);

			if(!empty($item_id))
			{
				$items = array($item_id);
			}
		}

		if (is_array($items) && count($items))
		{
			$this->auth->restrict('Storylines.Content.Manage');

			foreach ($items as $id)
			{
				$item = $this->storylines_articles_model->find($id);
				
				if (isset($item))
				{
					$storyline_id = $item->storyline_id;
					if ($this->storylines_articles_model->delete($id))
					{
						$this->load->model('activities/Activity_model', 'activity_model');

						//$item = $this->storylines_articles_model->find($id);
						$user = $this->user_model->find($this->current_user->id);
						$log_name = $this->settings_lib->item('auth.use_own_names') ? $this->current_user->username : ($this->settings_lib->item('auth.use_usernames') ? $user->username : $user->email);
						$this->activity_model->log_activity($this->current_user->id, lang('us_log_delete') . ': '.$log_name, 'storylines');
						Template::set_message('The article was successfully deleted.', 'success');
						
						if (in_array('comments',module_list(true))) {
							modules::run('comments/purge_thread',$item->comments_thread_id);
						}
			
					} else {
						Template::set_message(lang('us_action_not_deleted'). $this->storylines_articles_model->error, 'error');
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
		if ($storyline_id != -1) {
			redirect(SITE_AREA .'/custom/storylines/edit/'.$storyline_id);
		}
		else
		{
			redirect(SITE_AREA .'/custom/storylines/');
		}
	}
	/*
		Method:
			get_article_results()
		
		Returns a JSON object of article results
		
		Parameters:
			$article_id		- Article ID int as Segement 6
			
		Return:
			JSON Array of result items
			
	*/
	public function get_article_results()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		$article_id = $this->uri->segment(6);
		
		if (isset($article_id) && !empty($article_id)) 
		{
			$json_out['result']['items'] = $this->storylines_articles_model->get_article_results($article_id);
		}
		else
		{
			$error = true;
			$status = "Article ID was missing.";
		}
		if ($error) 
		{ 
			$json_out['code'] = 301;
			$json_out['status'] = "error:".$status; 
			$json_out['result'] = 'An error occured.';
		}
		$this->output->set_header('Content-type: application/json'); 
		$this->output->set_output(json_encode($json_out));

	}	/*
		Method:
			get_article_predecessors()
		
		Returns a JSON object of article predecessors
		
		Parameters:
			$article_id		- Article ID int as Segement 6
			
		Return:
			JSON Array of result items
			
	*/
	public function get_article_predecessors()
	{
		$error = false;
		$json_out = array("result"=>array(),"code"=>200,"status"=>"OK");
		
		$article_id = $this->uri->segment(6);
		
		if (isset($article_id) && !empty($article_id)) 
		{
			$json_out['result']['items'] = $this->storylines_articles_model->get_article_predecessors($article_id);
		}
		else
		{
			$error = true;
			$status = "Article ID was missing.";
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

	private function save_article($type='insert', $id = 0)
	{
		//$db_dbprefix = $this->db->dbdbprefix;

		$this->form_validation->set_rules('title', lang('sl_title'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('description', lang('sl_description'), 'trim|strip_tags|xss_clean');
		$this->form_validation->set_rules('subject', lang('sl_subject'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('text', lang('sl_text'), 'required|trim|strip_tags|xss_clean');
		$this->form_validation->set_rules('reply', lang('sl_reply'), 'strip_tags|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('in_game_message', lang('sl_in_game_message'), 'required|strip_tags|numeric|trim|xss_clean');
		$this->form_validation->set_rules('predecessor_id', lang('sl_predecessor'), 'strip_tags|numeric|trim|xss_clean');

		if ($this->form_validation->run() === false)
		{
			return false;
		}
		// INTERACTIVE REPLY VALIDATION
		if ($this->input->post('reply'))
		{
			$reply = $this->input->post('reply');
			$subject = $this->input->post('subject');
			$text = $this->input->post('text');
			if ($reply != $subject || $reply != $text || $subject != $text)
			{
				$this->save_error = 'When using interactive replies, the subject, text and reply must all be the same value. Please correct your article to continue submitting.';
				return false;
			}
			$mess =$this->input->post('in_game_message');
			if (!empty($reply) && $mess != 3)
			{
				$this->save_error = 'When using interactive replies, the In Game Message must be set to <b>No Message</b>. Please correct your article to continue submitting.';
				return false;
			}
		}
		
		$data = array(
					'title'=>$this->input->post('title'),
					'description'=>$this->input->post('description'),
					'subject'=>$this->input->post('subject'),
					'text'=>$this->input->post('text'),
					'reply'=>$this->input->post('reply'),
					'in_game_message'=>($this->input->post('in_game_message')) ? (int)$this->input->post('in_game_message') : 1
		);
		
		
		
		$success = null;
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
			$data = $data + array('storyline_id'=>(int)$this->input->post('storyline_id'),
								  'comments_thread_id'=>$thread_id,
								  'created_by'=>$this->current_user->id);
			$id_success = $this->storylines_articles_model->insert($data);
			$id = $this->db->insert_id();
		}
		else	// Update
		{
			$data = $data + array('modified_by'=>$this->current_user->id);
			$id_success = $this->storylines_articles_model->update($id, $data);
		}

		$id_list = false;
		if ($this->input->post('predecessor_id'))
		{
			$id_list = array($this->input->post('predecessor_id'));

		}
		else if ($this->input->post('pred_ids'))
		{
			$id_list =  $this->input->post('pred_ids');
		}
		//echo("id_list length = ".sizeof($id_list)."<br />\n");
		// SET or update article predecessors
		if ($id_success !== false && $id_list !== false)
		{
			$pred_ids = array();
			if (is_array($id_list) && count($id_list))
			{
				foreach ($id_list as $pred_id)
				{
					array_push($pred_ids, array('storyline_id'=>$this->input->post('storyline_id'),
												'article_id' => $id,
												'predecessor_id' => $pred_id));
				}
				$success = $this->storylines_articles_model->set_article_predecessors($id,$pred_ids);
				$this->load->model('storylines_history_model');
				$this->storylines_history_model->insert(array('var_id'=>$id,'object_type'=>2,'added'=>'Predecessor(s): '.implode(",",$id_list),'created_by'=>$this->current_user->id));

			}
		}
		return $id;
	}
}
// End main module class