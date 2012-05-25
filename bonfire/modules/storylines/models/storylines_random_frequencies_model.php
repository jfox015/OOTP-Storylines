<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_random_frequencies_model
	
*/

class Storylines_random_frequencies_model extends BF_Model 
{

	protected $table		= 'list_storylines_random_frequencies';
	protected $key			= 'id';
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	public function list_as_select()
	{
		$arrOut = array();
		$results = $this->select('name,value')->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				$arrOut[$result->value] = $result->name;
			}
		}
		return $arrOut;
	}
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

}