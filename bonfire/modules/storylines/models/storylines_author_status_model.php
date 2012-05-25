<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_author_status_model
	
*/

class Storylines_author_status_model extends BF_Model 
{

	protected $table		= 'list_storylines_author_status';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= '';
	protected $set_created	= false;
	protected $set_modified = false;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	public function list_as_select()
	{
		$arrOut = array();
		$results = $this->select('id,name')->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				$arrOut[$result->id] = $result->name;
			}
		}
		return $arrOut;
	}
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

}