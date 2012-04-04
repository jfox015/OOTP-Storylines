<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_model
	
*/

class Storylines_model extends BF_Model 
{

	protected $table		= 'storylines';
	protected $key			= 'id';
	protected $soft_deletes	= true;
	protected $date_format	= 'int';
	protected $set_created	= true;
	protected $set_modified = true;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	public function find($value= null)
	{
		$dbprefix = $this->db->dbprefix;
		$this->join('list_storylines_categories','list_storylines_categories.id = storylines.category_id');
		$this->join('list_storylines_publish_status','list_storylines_publish_status.id = storylines.publish_status_id');
		$this->join('list_storylines_review_status','list_storylines_review_status.id = storylines.review_status_id');
		$this->select('storylines.id, storylines.description, random_frequency, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.publish_status_id, list_storylines_publish_status.name as status_name,  storylines.review_status_id, list_storylines_review_status.name as review_status_name, comments_thread_id, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');
		return parent::find($value);
	}
	public function find_all()
	{
		$dbprefix = $this->db->dbprefix;
		$this->join('list_storylines_categories','list_storylines_categories.id = storylines.category_id');
		$this->join('list_storylines_publish_status','list_storylines_publish_status.id = storylines.publish_status_id');
		$this->select('storylines.id, storylines.description, random_frequency, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.publish_status_id, list_storylines_publish_status.name as status_name, comments_thread_id, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');
		return parent::find_all();
	}
	
	public function add_data_object($data = false)
	{
		if ($data === false)
		{
			return false;
		}
		$this->db->insert('storylines_data_objects',$data);
		return ($this->db->affected_rows() > 0);
	}
	public function remove_data_object($object_id = false)
	{
		if ($object_id === false)
		{
			return false;
		}
		$this->db->delete('storylines_data_objects',$object_id);
		return ($this->db->affected_rows() > 0);
	}
	public function get_data_objects($storyline_id = false)
	{
		if ($storyline_id === false)
		{
			return false;
		}
		$dbprefix = $this->db->dbprefix;
		$query = $this->db->select('storylines_data_objects.id, list_storylines_data_objects.name, list_storylines_data_objects.slug, list_storylines_data_objects.description, (SELECT COUNT('.$dbprefix.'storylines_conditions.id) FROM '.$dbprefix.'storylines_conditions LEFT JOIN '.$dbprefix.'storylines_data_objects ON '.$dbprefix.'storylines_data_objects.id = '.$dbprefix.'storylines_conditions.var_id WHERE '.$dbprefix.'storylines_conditions.level_type =2) as condition_count')
				 ->join('list_storylines_data_objects','list_storylines_data_objects.id = storylines_data_objects.object_id','right outer')
				 ->where('storyline_id',$storyline_id)
				 ->get('storylines_data_objects');
		
		$data_objects = array();
		if ($query->num_rows() > 0) 
		{
			$data_objects = $query->result();
		}
		$query->free_result();
		return $data_objects;
	
	}	
	public function add_trigger($data = false)
	{
		if ($data === false)
		{
			return false;
		}
		$this->db->insert('storylines_triggers',$data);
		return ($this->db->affected_rows() > 0);
	}
	public function remove_trigger($object_id = false)
	{
		if ($object_id === false)
		{
			return false;
		}
		$this->db->delete('storylines_triggers',array('id'=>$object_id));
		return ($this->db->affected_rows() > 0);
	}
	public function get_triggers($storyline_id = false)
	{
		$query = $this->db->select('storylines_triggers.id, list_storylines_triggers.name, list_storylines_triggers.slug')
				 ->join('list_storylines_triggers','list_storylines_triggers.id = storylines_triggers.trigger_id','right outer')
				 ->where('storyline_id',$storyline_id)
				 ->get('storylines_triggers');
		
		$triggers = array();
		if ($query->num_rows() > 0) 
		{
			$triggers = $query->result();
		}
		$query->free_result();
		return $triggers;
	
	}
	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------
}