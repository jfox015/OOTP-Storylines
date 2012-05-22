<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_history_model
	
*/

class Storylines_history_model extends BF_Model 
{

	protected $table		= 'storylines_history';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= 'int';
	protected $set_created	= true;
	protected $set_modified = false;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
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
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

}