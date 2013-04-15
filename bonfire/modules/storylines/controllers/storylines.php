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
class Storylines extends Front_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_category_model');
		
		$this->load->helper('storylines');
		$this->lang->load('storylines');
		
		$this->load->helper('application');

		$this->load->library('template');
		$this->load->library('assets');
		$this->load->library('ui/contexts');

	}

	//--------------------------------------------------------------------
	
	public function index()
	{
		
		$this->limit = $this->settings_lib->item('site.list_limit');

		$categories = $this->storylines_category_model->select('id, name')->find_all();
        Template::set('categories', $categories);
		
		// acessing our userdata cookie
		$cookie = unserialize($this->input->cookie($this->config->item('sess_cookie_name')));
		$logged_in = isset ($cookie['logged_in']);
		unset ($cookie);
		
		$username = ''; // SITE username
		$user_name = ''; // FULL NAME
		if (!isset($this->auth)) 
		{
			// Auth setup
			$this->load->model('users/User_model', 'user_model');
			$this->load->library('users/auth');
		}
		if ($logged_in) 
		{
			$username = $this->current_user->username;
			$user_name = "(".$this->current_user->display_name.")";
		}
		Template::set('logged_in', $logged_in);
		Template::set('username', $username);
		Template::set('user_name', $user_name);
		
		$this->load->helper('ui/ui');
		$this->load->helper('form');
		$dbprefix = $this->db->dbprefix;
		$offset = $this->uri->segment(2);
		
        // Do we have any actions?
        if ($action = $this->input->post('submit'))
        {
            $checked = $this->input->post('checked');
			
			$this->load->module('storylines/custom');
			
            switch(strtolower($action))
            {
                case 'flag':
                    $this->custom->flag($checked, '', true);
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
			default:
				break;
        }
		$where['storylines.publish_status_id'] = 3;
        $where['storylines.deleted'] = 0;
        $this->storylines_model->limit($this->limit, $offset)->where($where);
		
		Template::set('storylines', $this->storylines_model->find_all());
		
        // Pagination
        $this->load->library('pagination');

        $this->storylines_model->where($where);
        $total_stories = $this->storylines_model->count_all();

        // Pagination config
		$this->pager = array();
		$this->pager['full_tag_open']	= '<div class="pagination pagination-right"><ul>';
		$this->pager['full_tag_close']	= '</ul></div>';
		$this->pager['next_link'] 		= '&rarr;';
		$this->pager['prev_link'] 		= '&larr;';
		$this->pager['next_tag_open']	= '<li>';
		$this->pager['next_tag_close']	= '</li>';
		$this->pager['prev_tag_open']	= '<li>';
		$this->pager['prev_tag_close']	= '</li>';
		$this->pager['first_tag_open']	= '<li>';
		$this->pager['first_tag_close']	= '</li>';
		$this->pager['last_tag_open']	= '<li>';
		$this->pager['last_tag_close']	= '</li>';
		$this->pager['cur_tag_open']	= '<li class="active"><a href="#">';
		$this->pager['cur_tag_close']	= '</a></li>';
		$this->pager['num_tag_open']	= '<li>';
		$this->pager['num_tag_close']	= '</li>';
		$this->pager['base_url'] = site_url('/storylines/index');
        $this->pager['total_rows'] = $total_stories;
        $this->pager['per_page'] = $this->limit;
        $this->pager['uri_segment']	= 2;

        $this->pagination->initialize($this->pager);

		Template::set('current_url', current_url());
        Template::set('filter', $filter);

        Template::render();
	}

	//--------------------------------------------------------------------
	
	public function details()
	{
		$storyline_id = $this->uri->segment(3);
		Template::set('storyline', $this->storylines_model->find($storyline_id));
		Template::render();
	}
}

// End main module class