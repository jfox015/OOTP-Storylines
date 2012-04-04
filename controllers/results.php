<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Results extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_results_model');
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
                $where['list_storylines_results.active'] = 0;
                break;
            default:
                $where['list_storylines_results.active'] = 1;
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_results_model->limit($this->limit, $offset)->where($where);
        $this->storylines_results_model->find_all();

        Template::set('storylines', $this->storylines_results_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_results_model->where($where);
        $total_stories = $this->storylines_results_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/results/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set_view('storylines/custom/results');
        Template::set('toolbar_title', lang('sl_results'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_result())
			{
				$article = $this->storylines_results_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/results');

				Template::set_message('Storyline Condition successfully created.', 'success');
				Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline result: '. $this->storylines_results_model->error);
			}
		}
        
		Template::set('categories', $this->storylines_results_model->categories_as_select());
		Template::set('types', $this->storylines_results_model->types_as_select());
		Template::set('value_types', $this->storylines_results_model->value_types_as_select());
		Template::set('toolbar_title', lang('sl_create_result'));
		Template::set_view('storylines/custom/result_form');
		Template::render();
	}
	
	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		$result_id = $this->uri->segment(6);

		if (empty($result_id))
		{
			Template::set_message(lang('sl_empty_id'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_result('update', $result_id))
			{
				$result = $this->storylines_results_model->find($result_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline Condition successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the Storyline Condition: '. $this->storylines_results_model->error);
			}
		}

		$result = $this->storylines_results_model->find($result_id);
		
		if (isset($result))
		{
			Template::set('result', $result);
			Template::set('categories', $this->storylines_results_model->categories_as_select());
			Template::set('value_types', $this->storylines_results_model->value_types_as_select());
		}
		else
		{
			Template::set_message(lang('sl_no_result_matches'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}
		
		Template::set('toolbar_title', lang('sl_edit_result'));
		Template::set_view('storylines/custom/result_form');
		Template::render();
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
			$this->storylines_results_model->update($item_id, array('active' => $active));
		}
	}
	//--------------------------------------------------------------------

	private function save_result($type='insert', $id = 0)
	{
		$db_prefix = $this->db->dbprefix;

		$this->form_validation->set_rules('name', lang('sl_name'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('slug', lang('sl_slug'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('description', lang('sl_description'), 'strip_tags|trim|max_length[1000]|xss_clean');
		$this->form_validation->set_rules('options', lang('sl_options'), 'strip_tags|trim|max_length[500]|xss_clean');
		$this->form_validation->set_rules('rules', lang('sl_rules'), 'strip_tags|trim|max_length[500]|xss_clean');
		$this->form_validation->set_rules('category_id', lang('sl_category'), 'strip_tags|numeric|trim|xss_clean');
		$this->form_validation->set_rules('value_type', lang('sl_value_type'), 'strip_tags|numeric|trim|xss_clean');
		$this->form_validation->set_rules('value_range_min', lang('sl_value_range_min'), 'strip_tags|numeric|trim|xss_clean');
		$this->form_validation->set_rules('value_range_max', lang('sl_value_range_max'), 'strip_tags|numeric|trim|xss_clean');
		
		if ($this->form_validation->run() === false)
		{
			return false;
		}
		$data = array(
				'name'=>$this->input->post('name'),
				'slug'=>$this->input->post('slug'),
				'description'=>($this->input->post('description')) ? $this->input->post('description') : '',
				'options'=>($this->input->post('options')) ? $this->input->post('options') : '',
				'rules'=>($this->input->post('rules')) ? $this->input->post('rules') : '',
				'category_id'=>($this->input->post('category_id')) ? $this->input->post('category_id') : 0,
				'value_type'=>($this->input->post('value_type')) ? $this->input->post('value_type') : 0,
				'value_range_min'=>($this->input->post('value_range_min')) ? $this->input->post('value_range_min') : 0,
				'value_range_max'=>($this->input->post('value_range_max')) ? $this->input->post('value_range_max') : 0
		);
		if ($this->input->post('activate')) $data['active'] = 1;
		if ($this->input->post('deactivate')) $data['active'] = 0;
		
		if ($type == 'insert')
		{
			return $this->storylines_results_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_results_model->update($id, $data);
		}
	}
}
// End main module class