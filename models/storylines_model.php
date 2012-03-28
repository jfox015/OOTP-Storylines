<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_model
	
*/

class Storylines_model extends BF_Model 
{

	protected $table		= 'storylines';
	protected $key			= 'id';
	protected $soft_deletes	= true;
	protected $date_format	= 'int';
	protected $set_created	= true;
	protected $set_modified = true;
	
	/*-----------------------------------------------
	/	PUBLIC FUNCTIONS
	/----------------------------------------------*/
	
	/*-----------------------------------------------
	/	PRIVATE FUNCTIONS
	/----------------------------------------------*/

}