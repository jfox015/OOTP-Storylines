<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_category_model
	
*/

class Storylines_conditions_model extends BF_Model 
{

	protected $table		= 'list_storylines_conditions';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= '';
	protected $set_created	= false;
	protected $set_modified = false;
	
	/*-----------------------------------------------
	/	PUBLIC FUNCTIONS
	/----------------------------------------------*/
	public function range_as_select_by_category($range = false)
	{
		if ($range === false) {
			return false;
		}
		$range_str = '';
		if (is_array($range))
		{
			$range_str = "(".implode(",",$range).")";
		}
		else if (is_string($range))
		{
			if (!strpos($range,"(") === false ) 
			{
				$range_str = "(".$range;
			}
			if (!strpos($range,")") === false ) 
			{
				$range_str .= ")";
			}
		}
		$this->db->where_in('category_id',$range_str);
		$arrOut = array();
		$results = $this->select('id, name, slug')->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				$arrOut[$result->id] = $result->name;
			}
		}
		return $arrOut;
	}
	
	public function list_as_select_by_category($category_id = false)
	{
		if ($category_id === false) 
		{
			return false;
		}
		$arrOut = array();
		$this->db->where('category_id', $category_id);
		$results = $this->select('id, name, slug')->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				if (!isset($result->name) || empty($result->name))
				{
					$name = $result->slug;
				}
				else
				{
					$name = $result->name;
				}
				$arrOut[$result->id] = $name;
			}
		}
		return $arrOut;
	}
	/*-----------------------------------------------
	/	PRIVATE FUNCTIONS
	/----------------------------------------------*/

}