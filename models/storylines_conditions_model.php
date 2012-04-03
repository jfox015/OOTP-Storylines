<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_conditions_model
	
*/

class Storylines_conditions_model extends BF_Model 
{

	protected $table		= 'list_storylines_conditions';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= '';
	protected $set_created	= false;
	protected $set_modified = false;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	
	public function get_object_conditions($object_id = false, $level_type = 3)
	{
		if ($object_id === false)
		{
			return false;
		}
		$this->where('level_type',$level_type);
		$this->where('var_id',$object_id);
		$this->select('*');
		$query = $this->db->get('storylines_conditions');
		$conditions = array();
		if ($query->num_rows() > 0)
		{
			$conditions = $query->result();
		}
		$query->free_result();

		return $conditions;
	}
	
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
	public function list_by_range()
	{
		$arrOut = array();
		$results = $this->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				$arrOut[$result->id] = $result->name;
			}
		}
		return $arrOut;
	}

	public function list_as_select_by_category($category_id = false, $level_id = false)
	{
		if ($category_id !== false) 
		{
			$this->db->where('category_id', $category_id);
		}
		if ($level_id !== false) 
		{
			$this->db->where('level_id', $level_id);
		}
		$arrOut = array();
		
		$this->db->select('id, name, slug, category_id')->order_by('category_id','asc');
		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0)
		{
			$curr_cat = 0;
			$cat_label = '';
			$sub_array = array();
			$category_names = $this->get_category_names();
			foreach ($query->result() as $row)
			{
				if ($row->category_id != $curr_cat)
				{
					$curr_cat = $row->category_id;
					$cat_label = $category_names[$curr_cat];
					if (count($sub_array) > 0)
					{
						$arrOut[$cat_label] = $sub_array;
						$sub_array = array();
					}
				}
				if (!isset($row->name) || empty($row->name))
				{
					$name = $row->slug;
				}
				else
				{
					$name = $row->name;
				}
				$sub_array[$row->id] = $name;
			}
			if (count($sub_array) > 0)
			{
				$arrOut[$cat_label] = $sub_array;
			}
		}
		$query->free_result();
		return $arrOut;
	}
	public function categories_as_select()
	{
		return $this->data_as_select('list_storylines_conditions_categories');
	}
	public function levels_as_select()
	{
		return $this->data_as_select('list_storylines_conditions_levels');
	}
	public function types_as_select()
	{
		return $this->data_as_select('list_storylines_conditions_types');
	}
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------
	private function data_as_select($table = false)
	{
		if ($table === false) {
			return;
		}
		$arrOut = array();
		$this->db->select('id, name');
		$query = $this->db->get($table);
		if ($query->num_rows() > 0) 
		{
			foreach($query->result() as $row) 
			{
				$arrOut[$row->id] = $row->name;
			}
		}
		return $arrOut;
	}
	private function get_category_name($category_id = false) 
	{
		if ($category_id === false) {
			return;
		}
		return $this->select('name')->find($category_id);
	}	
	private function get_category_names() 
	{
		$this->db->select('id, name');
		$query = $this->db->get('list_storylines_conditions_categories');
		
		$category_names = array();
		if ($query->num_rows() > 0) 
		{
			foreach($query->result() as $row) 
			{
				$category_names[$row->id] = $row->name;
			}
		}
		$query->free_result();
		
		return $category_names;
	}
}