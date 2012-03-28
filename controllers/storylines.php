<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Storylines extends Front_Controller {

	//--------------------------------------------------------------------
	
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('storylines_model');
		$this->load->model('storylines_category_model');
	}

	//--------------------------------------------------------------------
	
	public function index()
	{
		Template::render();
	}
}

// End main module class