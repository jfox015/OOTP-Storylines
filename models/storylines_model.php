<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2012 Jeff Fox

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
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
	/*
		Method:
			Delete()
			
		A function to remove the stroyline. it must recurisvely remove all related data like 
		triggers, conditions, data objects, tokens and articles.
		
		Parematers:
			storyline_id - Sotrlyine ID int
			
		Result:
			TRUE on success, FALSE on failure
	*/
	public function delete($storyline_id = false)
	{
		if ($storyline_id === false)
		{
			$this->error = 'No storyline ID was received.';
			return false;
		}
		$this->load->model('storylines_history_model');
		$this->load->model('storylines_conditions_model');
		$this->load->model('storylines_articles_model');
		$this->load->model('storylines_triggers_model');
		$this->load->model('storylines_data_objects_model');
		$this->load->model('storylines_results_model');
		$this->load->model('storylines_article_predecessors');
		
		$articles = $this->storylines_articles_model->get_all_articles($storyline_id);
		$data_objects = $this->get_data_objects_ids($storyline_id);
		$this->storylines_history_model->batch_delete($articles, 2);
		$this->storylines_conditions_model->batch_delete($data_objects, 3);
		$this->storylines_conditions_model->batch_delete($articles, 2);
		$this->storylines_conditions_model->batch_delete($storyline_id);
		$this->storylines_articles_model->delete_predecessors('storyline_id',$storyline_id);
		$this->storylines_articles_model->delete_where('storyline_id',$storyline_id);
		$this->storylines_results_model->delete_where('storyline_id',$storyline_id);
		$this->storylines_triggers_model->delete_where('storyline_id',$storyline_id);
		$this->storylines_data_objects_model->delete_where('storyline_id',$storyline_id);
		
		parent::delete($storyline_id);
	}
	
	public function get_complete_storylines($storyline_id = false, $author_status_id = false, $publish_status_id = false)
	{
		if ($storyline_id !== false)
		{
			if (is_array($storyline_id))
			{
				$storyline_id_str = "(".implode(",",$storyline_id).")";
				$this->where_in('storyline_id',$storyline_id_str);
			}
			else
			{
				$this->where('storyline_id',$storyline_id);
			}
		}
		if ($author_status_id !== false)
		{
			if (is_array($author_status_id))
			{
				$author_id_str = "(".implode(",",$author_status_id).")";
				$this->where_in('author_status_id',$author_id_str);
			}
			else
			{
				$this->where('author_status_id',$author_status_id);
			}
		}
		if ($publish_status_id !== false)
		{
			if (is_array($publish_status_id))
			{
				$publish_id_str = "(".implode(",",$publish_status_id).")";
				$this->where_in('publish_status_id',$publish_id_str);
			}
			else
			{
				$this->where('publish_status_id',$publish_status_id);
			}
		}
		$this->select('id, name, random_frequency');
		$storylines = $this->find_all();
		
		if (count($storylines) > 0)
		{
			$this->load->model('storylines_conditions_model');
			$this->load->model('storylines_articles_model');
			foreach($storylines as $storyline)
			{
				$storyline->triggers = $this->get_triggers($storyline->id);
				$storyline->conditions = $this->storylines_conditions_model->get_object_conditions($storyline->id, 1);
				
				$data_objects = $this->data_objects($storyline->id);
				if (count($data_objects) > 0)
				{
					foreach($data_objects as $data_object)
					{
						$data_object->conditions = $this->storylines_conditions_model->get_object_conditions($data_object->id, 3);
					}
				}
				$storyline->data_objects = $data_objects;
				
				// GET ARTICLES
				$articles = $this->storylines_articles_model->get_all_articles($storyline_id);
				if (count($articles) > 0)
				{
					foreach($articles as $article)
					{
						$article->conditions = $this->storylines_conditions_model->get_object_conditions($article->id, 2);
						$article->results = $this->storylines_articles_model->get_article_results($article->id);
						$article->predecessors = $this->storylines_articles_model->get_article_predecessors($article->id);
					}
				}
				$storyline->articles = $articles;
			}
		}
		return $storylines;
	}
	//---------------------------------------------------------
	//	!SPECIALIZED SEARCH FUNCTIONS
	//---------------------------------------------------------
	public function find($value= null)
	{
		$dbprefix = $this->db->dbprefix;
		$this->join('list_storylines_categories','list_storylines_categories.id = storylines.category_id');
		$this->join('list_storylines_publish_status','list_storylines_publish_status.id = storylines.publish_status_id');
		$this->join('list_storylines_author_status','list_storylines_author_status.id = storylines.author_status_id');
		$this->select('storylines.id, storylines.description, random_frequency, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.publish_status_id, list_storylines_publish_status.name as publish_status_name,  storylines.author_status_id, list_storylines_author_status.name as author_status_name, comments_thread_id, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');
		return parent::find($value);
	}
	public function find_all()
	{
		$dbprefix = $this->db->dbprefix;
		$this->join('list_storylines_categories','list_storylines_categories.id = storylines.category_id');
		$this->join('list_storylines_publish_status','list_storylines_publish_status.id = storylines.publish_status_id');
		$this->join('list_storylines_author_status','list_storylines_author_status.id = storylines.author_status_id');
		$this->select('storylines.id, storylines.description, random_frequency, storylines.category_id, list_storylines_categories.name as category_name, title, created_by, created_on, modified_on, modified_by, storylines.publish_status_id, list_storylines_publish_status.name as publish_status_name,  storylines.author_status_id, list_storylines_author_status.name as author_status_name, comments_thread_id, (SELECT COUNT('.$dbprefix.'storylines_articles.id) FROM '.$dbprefix.'storylines_articles WHERE '.$dbprefix.'storylines_articles.storyline_id = '.$dbprefix.'storylines.id) as article_count');
		return parent::find_all();
	}
	//---------------------------------------------------------

	//---------------------------------------------------------
	//	!DATA OBJECT AND TRIGGER AJAX FUNCTIONS
	//---------------------------------------------------------
	
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
		$this->db->delete('storylines_data_objects',array('id'=>$object_id));
		return ($this->db->affected_rows() > 0);
	}
	public function get_data_objects_ids($storyline_id = false)
	{
		if ($storyline_id === false)
		{
			return false;
		}
		$id_list = array();
		$data_objects = $this->get_data_objects($storyline_id);
		if (isset($data_objects) && is_array($data_objects) && count($data_objects))
		{
			foreach ($data_objects as $data_object)
			{
				array_push($id_list,$data_object->id);
			}
		}
		return $id_list;
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