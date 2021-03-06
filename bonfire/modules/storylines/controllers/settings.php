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
class Settings extends Admin_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->auth->restrict('Site.Settings.View');

        if (!class_exists('Activity_model'))
        {
            $this->load->model('activities/Activity_model', 'activity_model', true);
        }
        $this->lang->load('storylines');

	}
	
	//--------------------------------------------------------------------

	public function _remap($method) 
	{ 
		if (method_exists($this, $method))
		{
			$this->$method();
		}
	}
    //--------------------------------------------------------------------

    public function index()
    {
        if ($this->input->post('submit'))
        {
            if ($this->save_settings())
            {
                Template::set_message(lang('md_settings_saved'), 'success');
                redirect(SITE_AREA .'/settings/[module]');
            } else
            {
                Template::set_message(lang('md_settings_error'), 'error');
            }
        }
        // Read our current settings
        $settings = $this->settings_lib->find_all();
        Template::set('settings', $settings);

        Template::set('toolbar_title', lang('mod_settings_title'));
        Template::set_view('storylines/settings/index');
        Template::render();
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // !PRIVATE METHODS
    //--------------------------------------------------------------------

    private function save_settings()
    {

		$this->load->library('form_validation');

        $this->form_validation->set_rules('comments_enabled', lang('sl_comments_enabled'), 'trim|xss_clean');
        
        if ($this->form_validation->run() === false)
        {
            return false;
        }

		$data = array(
            array('name' => 'storylines.comments_enabled', 'value' => $this->input->post('comments_enabled')),

        );
        //destroy the saved update message in case they changed update preferences.
        if ($this->cache->get('update_message'))
        {
            if (!is_writeable(FCPATH.APPPATH.'cache/'))
            {
                $this->cache->delete('update_message');
            }
        }

        // Log the activity
        $this->activity_model->log_activity($this->auth->user_id(), lang('mod_act_settings_saved').': ' . $this->input->ip_address(), '[prefix]');

        // save the settings to the DB
        $updated = $this->settings_model->update_batch($data, 'name');

        return $updated;

	}
}

// End Settings Class