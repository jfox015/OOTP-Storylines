<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Articles extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_articles_model');
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
		$this->auth->restrict('Storylines.Content.Add');

		$storyline_id = $this->uri->segment(6);
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_article())
			{
				$article = $this->storylines_articles_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/articles');

				Template::set_message('Storyline Article successfully created.', 'success');
				$dest = '/custom/stoylines/edit/'.$storyline_id;
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
        
		Template::set('game_message_types', $this->storylines_articles_model->get_game_message_types());
		Template::set('storyline_id', $storyline_id);
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
				Template::set_message('There was a problem updating the Storyline Article: '. $this->storylines_articles_model->error);
			}
		}

		$article = $this->storylines_articles_model->find($article_id);
		
		if (isset($article))
		{
			Template::set('article', $article);
			Template::set_view('storylines/custom/articles/edit_article_form');
			$this->load->model('storylines_conditions_model');
			Template::set('conditions', $this->storylines_conditions_model->list_as_select_by_category());
			$this->load->model('storylines_results_model');
			Template::set('results', $this->storylines_results_model->find_all());
			Template::set('article_conditions', $this->storylines_articles_model->get_article_conditions($article_id));
			Template::set('article_results', $this->storylines_articles_model->get_article_results($article_id));
			Template::set('game_message_types', $this->storylines_articles_model->get_game_message_types());
			Template::set('storyline', $this->storylines_model->find($article->storyline_id));
			Template::set('comment_form', (in_array('comments',module_list(true))) ? modules::run('comments/thread_view_with_form',$article->comments_thread_id) : '');
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

	private function save_article($type='insert', $id = 0)
	{
		$db_prefix = $this->db->dbprefix;

		if ($type == 'insert')
		{
			$this->form_validation->set_rules('subject', lang('sl_subject'), 'required|trim|max_length[255]|xss_clean');
			$this->form_validation->set_rules('text', lang('sl_text'), 'required|trim|xss_clean');
			$this->form_validation->set_rules('reply', lang('sl_reply'), 'strip_tags|trim|xss_clean');
			$this->form_validation->set_rules('in_game_message', lang('sl_in_game_message'), 'required|strip_tags|numeric|trim|xss_clean');
		}
		if ($this->form_validation->run() === false)
		{
			return false;
		}
		$data = array(
					'subject'=>$this->input->post('subject'),
					'text'=>$this->input->post('text'),
					'reply'=>$this->input->post('reply'),
					'in_game_message'=>($this->input->post('in_game_message')) ? $this->input->post('in_game_message') : 1
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
			$data = $data + array('storyline_id'=>$this->input->post('storyline_id'),
								  'comments_thread_id'=>$thread_id);
			return $this->storylines_articles_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_articles_model->update($id, $data);
		}
	}
}
// End main module class