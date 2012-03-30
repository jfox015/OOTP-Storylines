<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_data_objects
	
*/

class Storylines_data_objects_model extends BF_Model
{

	protected $table		= 'storylines_data_objects';
	protected $key			= 'id';
	protected $soft_deletes	= false;
	protected $date_format	= 'int';
	protected $set_created	= false;
	protected $set_modified = false;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	public function find_all_by($field=null, $value=null)
	{
		$this->select('storylines_data_objects.id, list_storylines_data_objects.name, list_storylines_data_objects.description');
		$this->db->join('list_storylines_data_objects','list_storylines_data_objects.id = storylines_data_objects.object_id','right outer');
		return parent::find_all_by($field, $value);
	}
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

}