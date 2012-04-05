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
	
}