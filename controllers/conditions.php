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
class Conditions extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_conditions_model');
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
                $where['list_storylines_conditions.active'] = 0;
                break;
            default:
                $where['list_storylines_conditions.active'] = 1;
                break;
        }

        $this->load->helper('ui/ui');
		$dbprefix = $this->db->dbprefix;
        $this->storylines_conditions_model->join('list_storylines_conditions_levels','list_storylines_conditions_levels.id = list_storylines_conditions.level_id', 'right outer');
        $this->storylines_conditions_model->join('list_storylines_conditions_categories','list_storylines_conditions_categories.id = list_storylines_conditions.category_id', 'right outer');
        $this->storylines_conditions_model->limit($this->limit, $offset)->where($where);
        $this->storylines_conditions_model->select('list_storylines_conditions.id, list_storylines_conditions.name, slug, category_id, list_storylines_conditions_categories.id as category_id, list_storylines_conditions_categories.name as category_name, list_storylines_conditions_levels.id as level_id, list_storylines_conditions_levels.name as level_name');

        Template::set('conditions', $this->storylines_conditions_model->find_all());

        // Pagination
        $this->load->library('pagination');

        $this->storylines_conditions_model->where($where);
        $total_stories = $this->storylines_conditions_model->count_all();
		

        $this->pager['base_url'] = site_url(SITE_AREA .'/custom/storylines/conditions/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 5;

        $this->pagination->initialize($this->pager);

		$this->load->helper('storylines');
		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::set_view('storylines/custom/conditions');
        Template::set('toolbar_title', lang('sl_conditions'));
        Template::render();
    }

	//--------------------------------------------------------------------

	public function create()
	{
		$settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		if ($this->input->post('submit'))
		{
			if ($id = $this->save_condition())
			{
				$article = $this->storylines_conditions_model->find($id);
				
				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines/conditions');

				Template::set_message('Storyline Condition successfully created.', 'success');
				Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');	
			}
			else
			{
				Template::set_message('There was a problem creating the storyline condition: '. $this->storylines_conditions_model->error);
			}
		}
        
		Template::set('categories', $this->storylines_conditions_model->categories_as_select());
		Template::set('types', $this->storylines_conditions_model->types_as_select());
		Template::set('levels', $this->storylines_conditions_model->levels_as_select());
		Template::set('toolbar_title', lang('sl_create_condition'));
		Template::set_view('storylines/custom/condition_form');
		Template::render();
	}
	
	//--------------------------------------------------------------------

	public function edit()
	{
        $settings = $this->settings_model->select('name,value')->find_all_by('module', 'storylines');
		$this->auth->restrict('Storylines.Data.Manage');
		
		$condition_id = $this->uri->segment(6);

		if (empty($condition_id))
		{
			Template::set_message(lang('sl_empty_id'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}

		if ($this->input->post('submit'))
		{
			if ($this->save_condition('update', $condition_id))
			{
				$condition = $this->storylines_conditions_model->find($condition_id);

				$this->load->model('activities/activity_model');
				$this->activity_model->log_activity($this->auth->user_id(), lang('us_log_create').' '.$this->current_user->display_name, 'storylines');

				Template::set_message('Storyline Condition successfully updated.', 'success');
			}
			else
			{
				Template::set_message('There was a problem updating the Storyline Condition: '. $this->storylines_conditions_model->error);
			}
		}

		$condition = $this->storylines_conditions_model->find($condition_id);
		
		if (isset($condition))
		{
			Template::set('condition', $condition);
			Template::set('categories', $this->storylines_conditions_model->categories_as_select());
			Template::set('types', $this->storylines_conditions_model->types_as_select());
			Template::set('levels', $this->storylines_conditions_model->levels_as_select());
		}
		else
		{
			Template::set_message(lang('sl_no_condition_matches'), 'error');
			Template::redirect(SITE_AREA .'/custom/storylines/manage_data/');
		}
		
		Template::set('toolbar_title', lang('sl_edit_condition'));
		Template::set_view('storylines/custom/condition_form');
		Template::render();
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
			$this->storylines_conditions_model->update($item_id, array('active' => $active));
		}
	}
	//--------------------------------------------------------------------

	private function save_condition($type='insert', $id = 0)
	{
		$db_prefix = $this->db->dbprefix;

		$this->form_validation->set_rules('name', lang('sl_name'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('slug', lang('sl_slug'), 'required|trim|max_length[255]|xss_clean');
		$this->form_validation->set_rules('description', lang('sl_description'), 'strip_tags|trim|max_length[1000]|xss_clean');
		$this->form_validation->set_rules('type', lang('sl_type'), 'strip_tags|numeric|trim|xss_clean');
		$this->form_validation->set_rules('level_id', lang('sl_level'), 'strip_tags|numeric|trim|xss_clean');
		$this->form_validation->set_rules('category_id', lang('sl_category'), 'strip_tags|numeric|trim|xss_clean');
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
					'type'=>($this->input->post('type')) ? $this->input->post('type') : 0,
					'value_range_min'=>($this->input->post('value_range_min')) ? $this->input->post('value_range_min') : 0,
					'value_range_max'=>($this->input->post('value_range_max')) ? $this->input->post('value_range_max') : 0,
					'level_id'=>($this->input->post('level_id')) ? $this->input->post('level_id') : 0,
					'category_id'=>($this->input->post('category_id')) ? $this->input->post('category_id') : 0
		);
		
		if ($this->input->post('activate')) $data['active'] = 1;
		if ($this->input->post('deactivate')) $data['active'] = 0;
		
		if ($type == 'insert')
		{
			return $this->storylines_conditions_model->insert($data);
		}
		else	// Update
		{
			return $this->storylines_conditions_model->update($id, $data);
		}
	}
}
// End main module class