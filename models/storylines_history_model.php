<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_history_model
	
*/

class Storylines_history_model extends BF_Model 
{

	protected $table		= 'storylines_history';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= 'int';
	protected $set_created	= true;
	protected $set_modified = false;
	
	/*-----------------------------------------------
	/	PUBLIC FUNCTIONS
	/----------------------------------------------*/

	/*-----------------------------------------------
	/	PRIVATE FUNCTIONS
	/----------------------------------------------*/

}