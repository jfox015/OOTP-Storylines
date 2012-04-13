<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_results_model
	
*/

class Storylines_results_model extends BF_Model 
{

	protected $table		= 'list_storylines_results';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= '';
	protected $set_created	= false;
	protected $set_modified = false;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	
	//--------------------------------------------------------------------

	/*
		Method: find_all()

		Returns all results types and their associated category and type information.

		Parameters:
			$show_inactive	- If false, will only return active results options.

		Returns:
			An array of objects with each results's information.
	*/
	public function find_all($show_inactive=false)
	{
		if (empty($this->selects))
		{
			$this->select($this->table .'.*, list_storylines_result_categories.id as category_id, list_storylines_result_categories.name as category_name, list_storylines_result_value_types.id as result_type_id');
		}

		if ($show_inactive === false)
		{
			$this->db->where('list_storylines_results.active', 1);
		}

		$this->db->join('list_storylines_result_categories', 'list_storylines_result_categories.id = '.$this->table .'.category_id', 'right outer');
		$this->db->join('list_storylines_result_value_types', 'list_storylines_result_value_types.id = '.$this->table .'.value_type', 'right outer');

		return parent::find_all();
	}
	
	public function list_by_category($show_inactive = false)
	{
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
	public function get_results($article_id = false)
	{
		$conditions = array();
		$query = $this->db->select('storylines_article_results.id, storylines_article_results.result_id, list_storylines_results.slug, list_storylines_results.name, storylines_article_results.result_value, list_storylines_result_categories.name as category_name, list_storylines_results.value_type, list_storylines_result_value_types.name as type_name')
						  ->join('list_storylines_results','list_storylines_results.id = storylines_article_results.result_id','left')
						  ->join('list_storylines_result_categories','list_storylines_results.category_id = list_storylines_result_categories.id','right outer')
						  ->join('list_storylines_result_value_types','list_storylines_results.value_type = list_storylines_result_value_types.id','right outer')
						  ->where('article_id',$article_id)
						  ->get('storylines_article_results');
		if ($query->num_rows > 0) 
		{
			$conditions = $query->result();
		} 
		$query->free_result();
		return $conditions;
	}
	
	/*
		Method: add_object_result()

		Adds a result for the specified article ID.

		Parameters:
			$data	- Data array object

		Returns:
			TRUE on success, FALSE on error
	*/
	public function add_object_result($data = false)
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
		return $this->db->insert('storylines_results',$data);
	}
	
	/*
		Method: update_object_condition()

		Updates an existing result for the specified article ID.

		Parameters:
			$data	- Data array object

		Returns:
			TRUE on success, FALSE on error
	*/
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
		$this->db->where('article_id',$data['article_id'])
			     ->where('result_id',$data['result_id']);		  
		return $this->db->update('storylines_results',array('value'=>$data['value']));
	
	}
	public function categories_as_select()
	{
		return $this->data_as_select('list_storylines_result_categories');
	}
	
	public function value_types_as_select()
	{
		return $this->data_as_select('list_storylines_result_value_types');
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
		$query = $this->db->get('list_storylines_result_categories');
		
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