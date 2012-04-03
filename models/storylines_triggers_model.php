<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_triggers_model

*/

class Storylines_triggers_model extends BF_Model
{

	protected $table		= 'list_storylines_triggers';
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
		$results = $this->select('id, name, slug')->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				$name = (isset($result->name) && !empty($result->name)) ? $result->name : $result->slug;
				$arrOut[$result->id] = $name;
			}
		}
		return $arrOut;
	}
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

}