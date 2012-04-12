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
	public function add_object_condition($data = false)
	{
		if ($data === false)
		{
			$this->error = "No data was received.";
			return false;
		}
		else if (!is_array($data))
		{
			$this->error = "Data received was not in proper array format.";
			return false;
		} 
		return $this->db->insert('storylines_conditions',$data);
	
	}
	public function update_object_condition($data = false)
	{
		if ($data === false)
		{
			$this->error = "No data was received.";
			return false;
		}
		else if (!is_array($data))
		{
			$this->error = "Data received was not in proper array format.";
			return false;
		} 
		$this->db->where('var_id',$data['var_id'])
			     ->where('level_type',$data['level_type'])
			     ->where('condition_id',$data['condition_id']);		  
		return $this->db->update('storylines_conditions',array('value'=>$data['value']));
	
	}
	public function count_object_conditions($var_id = 0, $level_type = 1, $condition_id = 0)
	{
		return ($this->db->select('id')
						  ->from('storylines_conditions')
						  ->where('var_id',$var_id)
						  ->where('level_type',$level_type)
						  ->where('condition_id',$condition_id)
						  ->count_all_results()) > 0;
	}
	public function get_object_conditions($var_id = 0, $level_type = 1)
	{
		$conditions = array();
		$query = $this->db->select('storylines_conditions.id, storylines_conditions.condition_id, list_storylines_conditions.slug, list_storylines_conditions.name, storylines_conditions.value, list_storylines_conditions_categories.name as category_name, list_storylines_conditions.type_id, list_storylines_conditions_types.name as type_name')
						  ->join('list_storylines_conditions','list_storylines_conditions.id = storylines_conditions.condition_id','left')
						  ->join('list_storylines_conditions_categories','list_storylines_conditions.category_id = list_storylines_conditions_categories.id','right outer')
						  ->join('list_storylines_conditions_types','list_storylines_conditions.type_id = list_storylines_conditions_types.id','right outer')
						  ->where('var_id',$var_id)
						  ->where('level_type',$level_type)
						  ->get('storylines_conditions');
		if ($query->num_rows > 0) 
		{
			$conditions = $query->result();
		} 
		$query->free_result();
		return $conditions;
	}
	public function purge_object_conditions($data)
	{
		if ($data === false)
		{
			$this->error = "No data was received.";
			return false;
		}
		else if (!is_array($data))
		{
			$this->error = "Data received was not in proper array format.";
			return false;
		} 
		return $this->db->delete('storylines_conditions',$data);
	}
	
	public function batch_delete($var_id = false, $object_type = 1) 
	{
		if ($var_id === false)
		{
			$this->error = "No var id was received.";
			return;
		}
		if (is_array($var_id))
		{
			$str_ids = "(".implode(",",$var_id).")";
			$this->where_in('var_id',$str_ids);
		}
		else
		{
			$this->where('var_id',$var_id);
		}
		$this->where('object_type',$object_type);	
		$this->delete($this->table);

	}

	
	public function range_by_category($range = false, $show_inactive = false)
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
			if ($range != "all")
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
		}
		if (!empty($range_str))
		{
			$this->db->where_in('category_id',$range_str);
		}
		if ($show_inactive === false)
		{
			$this->where('active',1);
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
						array_push($arrOut, array('label'=>$cat_label, 'options'=>$sub_array));
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
				array_push($sub_array,array('id'=>$row->id, 'name'=>$name));
			}
			if (count($sub_array) > 0)
			{
				array_push($arrOut, array('label'=>$cat_label, 'options'=>$sub_array));
			}
		}
		$query->free_result();
		return $arrOut;
	}
	public function list_as_select($show_inactive = false)
	{
		$arrOut = array();
		if ($show_inactive === false)
		{
			$this->where('active',1);
		}
		$results = $this->select('id, name, slug')->find_all();
		if (sizeof($results) > 0)
		{
			foreach ($results as $result)
			{
				$strName = (isset($result->name) && !empty($result->name)) ? $result->name : $result->slug;
				$arrOut[$result->id] = $strName;
			}
		}
		return $arrOut;
	}

	public function list_as_select_by_category($category_id = false, $level_id = false, $show_inactive=false)
	{
		if ($category_id !== false) 
		{
			$this->db->where('category_id', $category_id);
		}
		if ($level_id !== false) 
		{
			$this->db->where('level_id', $level_id);
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