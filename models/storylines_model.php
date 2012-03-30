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
		$this->select('storylines.id, storylines.description, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.publish_status_id, list_storylines_publish_status.name as status_name,  storylines.review_status_id, list_storylines_review_status.name as review_status_name, comments_thread_id, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');
		return parent::find($value);
	}
	public function find_all()
	{
		$dbprefix = $this->db->dbprefix;
		$this->join('list_storylines_categories','list_storylines_categories.id = storylines.category_id');
		$this->join('list_storylines_publish_status','list_storylines_publish_status.id = storylines.publish_status_id');
		$this->select('storylines.id, storylines.description, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.publish_status_id, list_storylines_publish_status.name as status_name, comments_thread_id, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');
		return parent::find_all();
	}//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

}