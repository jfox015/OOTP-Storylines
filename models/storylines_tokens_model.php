<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_tokens_model

*/

class Storylines_tokens_model extends BF_Model
{

	protected $table		= 'list_storylines_tokens';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= '';
	protected $set_created	= false;
	protected $set_modified = false;

	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	public function list_as_select($show_inactive = false)
	{
		$arrOut = array();
		if ($show_inactive === false)
		{
			$this->db->where('list_storylines_results.active', 1);
		}
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
	
	public function list_as_select_by_category($category_id = false, $show_inactive = false)
	{
		if ($category_id !== false) 
		{
			$this->db->where('category_id', $category_id);
		}
		if ($show_inactive === false) 
		{
			$this->db->where('active',1);
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
		return $this->data_as_select('list_storylines_tokens_categories');
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
	private function get_category_names()
	{
		$this->db->select('id, name');
		$query = $this->db->get('list_storylines_tokens_categories');
		
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