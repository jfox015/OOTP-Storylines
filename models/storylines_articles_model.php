<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	Class: Storylines_articles_model
*/

class Storylines_articles_model extends BF_Model 
{

	protected $table		= 'storylines_articles';
	protected $key			= 'id';
	protected $soft_deletes	= true;
	protected $date_format	= 'datetime';
	protected $set_created	= true;
	protected $set_modified = true;
	
	//--------------------------------------------------------------------
	// !PUBLIC METHODS
	//--------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
	}
	//--------------------------------------------------------------------

	/*
		Method:
			build_article_tree()
			
		Builds a multidimensional array of articles with child articles nested
		as children attributes;
		
		Parameters:
			$storyline_id - Storyline ID int
			
		Return:
			Articles Array with nested child articles as Array->children
	
	*/
	public function build_article_tree($storyline_id = false)
	{
		if ($storyline_id === false)
		{
			$this->error .= "No storyline ID was received.<br/>\n";
			return false;
		}
		
		// Pull all starting articles without predecessors first
		$articles = $this->get_parent_articles($storyline_id);

		if (count($articles))
		{
			foreach ($articles as $article)
			{
				$article->children = $this->get_article_children_details($article->id);
			}
		}
		return $articles;
	}
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
	public function delete($article_id = false)
	{
		if ($article_id === false)
		{
			$this->error = 'No article ID was received.';
			return false;
		}
		$this->load->model('storylines_history_model');
		$this->load->model('storylines_conditions_model');
		$this->load->model('storylines_results_model');
		
		$this->storylines_history_model->batch_delete($article_id, 2);
		$this->storylines_conditions_model->batch_delete($article_id, 2);
		$this->storylines_results_model->delete_where('article_id',$article_id);
		
		$this->change_child_predecessors('article_id',$article_id);
		
		parent::delete($article_id);
	}
	
	//--------------------------------------------------------------------

	/*
		Method:
			delete_predecessors()
			
		Wipes the predecessor table of all predecessors for the passed storyline ID.
		
		Parameters:
			$storyline_id - Storyline ID int
			
		Return:
			TRUE on success, FALSE on error
	
	*/
	public function delete_predecessors($storyline_id) 
	{
		return $this->db->where('storyline_id',$storyline_id)->delete('storylines_article_predecessors');
	}
		
	//--------------------------------------------------------------------

	/*
		Method:
			get_alL_articles()
			
		Returns an array of article child IDs.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of child article IDs
	
	*/
	public function get_all_articles($storyline_id, $except_article_id = false, $include_content = true) 
	{
		if ($except_article_id !== false)
		{
			$this->db->where('id <> '.$except_article_id);
		}
		$select = '';
		if ($except_article_id !== false)
		{
			$select = 'text, description, reply,';
		}
		$prefix = $this->db->dbprefix;
		return $this->select($select.'storylines_articles.id, title, in_game_message, subject, 
							(SELECT COUNT('.$prefix.'storylines_conditions.id) FROM '.$prefix.'storylines_conditions WHERE '.$prefix.'storylines_conditions.var_id = '.$prefix.'storylines_articles.id AND level_type = 2) as condition_count,
							(SELECT COUNT('.$prefix.'storylines_article_results.id) FROM '.$prefix.'storylines_article_results WHERE '.$prefix.'storylines_article_results.article_id = '.$prefix.'storylines_articles.id) as result_count')
							->find_all_by('storyline_id',$storyline_id);
	}		
	//--------------------------------------------------------------------

	/*
		Method:
			get_alL_article_ids()
			
		Returns an array of article child IDs.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of child article IDs
	
	*/
	public function get_all_article_ids($storyline_id, $except_article_id = false) 
	{
		if ($except_article_id !== false)
		{
			$this->db->where('id <> '.$except_article_id);
		}
		return $this->select('id')->find_all_by('storyline_id',$storyline_id);
	}
	//--------------------------------------------------------------------

	/*
		Method:
			get_article_children()
			
		Returns an array of article child IDs.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of child article IDs
	
	*/
	public function get_article_children($article_id) 
	{
		$query = $this->db->select('article_id')
							 ->where('predecessor_id',$article_id)
							 ->get('storylines_article_predecessors');
		$children = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				array_push($children,$row->article_id);
			}
		}
		return $children;
	}
	//--------------------------------------------------------------------

	/*
		Method:
			get_article_predecessor_ids()
			
		Returns an array of predecessor ID values.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of result IDs
	
	*/
	public function get_article_predecessors($article_id = false)
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received.<br/>\n";
			return false;
		}
		$results = array();
		$prefix = $this->db->dbprefix;
		$this->db->select('storylines_article_predecessors.predecessor_id, title, subject, 
							(SELECT COUNT('.$prefix.'storylines_conditions.id) FROM '.$prefix.'storylines_conditions WHERE '.$prefix.'storylines_conditions.var_id = '.$prefix.'storylines_article_predecessors.predecessor_id AND level_type = 2) as condition_count,
							(SELECT COUNT('.$prefix.'storylines_article_results.id) FROM '.$prefix.'storylines_article_results WHERE '.$prefix.'storylines_article_results.article_id = '.$prefix.'storylines_article_predecessors.predecessor_id) as result_count')
				 ->join('storylines_articles', 'storylines_articles.id = storylines_article_predecessors.predecessor_id')
				 ->where('article_id', $article_id);
		$query = $this->db->get('storylines_article_predecessors');
		if ($query->num_rows() > 0) 
		{
			$results = $query->result();
		}
		$query->free_result();
		return $results;
	}		//--------------------------------------------------------------------

	/*
		Method:
			set_article_predecessors()
			
		Set the passed articles predecessors using an array of predecessor ID values.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of result IDs
	
	*/
	public function set_article_predecessors($article_id = false, $data = false)
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received.<br/>\n";
			return false;
		}
		if ($data === false)
		{
			$this->error .= "No data array was received.<br/>\n";
			return false;
		}
		// CLEAN OUT EXISTING PREDECESSORS
		$this->db->where('article_id',$article_id);
		$this->db->delete('storylines_article_predecessors');
		
		foreach ($data as $predecessor)
		{
			$this->db->insert('storylines_article_predecessors',$predecessor);
		}
		return true;
	}	
	
	//--------------------------------------------------------------------

	/*
		Method:
			get_article_predecessor_ids()
			
		Returns an array of predecessor ID values.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of result IDs
	
	*/
	public function get_article_predecessor_ids($article_id = false)
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received.<br/>\n";
			return false;
		}
		$results = array();
		$this->db->select('predecessor_id')
				 ->where('article_id', $article_id);
		$query = $this->db->get('storylines_article_predecessors');
		if ($query->num_rows() > 0) 
		{
			foreach($query->result() as $row)
			{
				array_push($results, $row->predecessor_id);
			}
		}
		$query->free_result();
		return $results;
	}
	//--------------------------------------------------------------------

	/*
		Method:
			get_article_conditions()
			
		Returns an array of result types and values.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of result IDs and values
	
	*/
	public function get_article_conditions($article_id = false)
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received.<br/>\n";
			return false;
		}
		$results = array();
		$this->db->select('*')
				 ->where('var_id', $article_id);
		$query = $this->db->get('storylines_conditions');
		if ($query->num_rows() > 0) 
		{
			$results = $query->result();
		}
		$query->free_result();
		return $results;
	}
	
	//--------------------------------------------------------------------

	/*
		Method:
			get_article_results()
			
		Returns an array of result types and values.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of result IDs and values
	
	*/
	public function get_article_results($article_id = false)
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received.<br/>\n";
			return false;
		}
		$results = array();
		$this->db->select('id, result_id, result_value')
				 ->where('article_id', $article_id);
		$query = $this->db->get('storylines_article_results');
		if ($query->num_rows() > 0) 
		{
			$results = $query->result();
		}
		$query->free_result();
		return $results;
	}	
	//--------------------------------------------------------------------

	/*
		Method:
			get_article_results()
			
		Returns an array of result types and values.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of result IDs and values
	
	*/
	public function get_article_results_for_form($article_id = false)
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received.<br/>\n";
			return false;
		}
		$results = array();
		$this->db->select('storylines_article_results.id, list_storylines_results.slug, result_id, result_value')
				 ->join('list_storylines_results','list_storylines_results.id = storylines_article_results.result_id','left outer')
				 ->not_like("slug",'%modifier')
				 ->not_like("slug",'%talent%')
				 ->not_like("slug",'%change%')
				 ->where('article_id', $article_id);
		$query = $this->db->get('storylines_article_results');
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result() as $row)
			{
				$results[$row->slug] = $row;
			}
		}
		$query->free_result();
		return $results;
	}
	
	//--------------------------------------------------------------------

	/*
		Method:
			get_game_message_types()
			
		Returns a select box ready array of game message type values
			
		Return:
			Array of value in id => value format
	
	*/
	public function get_game_message_types()
	{
		$query = $this->db->select('id, name')->get( 'list_storylines_articles_message_types' );

		if ( $query->num_rows() <= 0 )
			return '';

		$option = array();

		foreach ($query->result() as $row)
		{
		  $row_id          = (int) $row->id;
		  $option[$row_id] = $row->name;
		}

		$query->free_result();

		return $option;
	
	}
	
	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------
	
	private function change_child_predecessors($article_id)
	{
		$parent_id = $this->get_parent_article($article_id);
		$children = $this->get_article_children($article_id);
		if (count($children))
		{
			foreach($children as $child_id)
			{
				$this->db->where('article_id',$child_id)
						 ->update('storylines_article_predecessors',array('predecessor_id',$parent_id));
			}
		}
		return true;
	}
	/*
		Method:
			get_article_details()
			
		Fetches the content field  for the given article ID.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Articles Return object
	
	*/
	private function get_article_details($article_id = false) 
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received to retrieve details.<br/>\n";
			return false;
		}
		return $this->select('subject, storyline_id, wait_days_min, wait_days_max, in_game_message, comment_thread_id, created_on, modified_on, deleted')
					->find($article_id);
	}
	/*
		Method:
			get_parent_article()
			
		Returns the id of the article predecessor.
		
		Parameters:
			$article_id - Article ID int
			
		Return:
			Parent article ID or 0 if not found,
	
	*/
	private function get_parent_article($article_id = false) 
	{
		$parent_id = 0;
		$query = $this->db->select('predecessor_id')
							 ->where('article_id',$article_id)
							 ->get('storylines_article_predecessors');
		if ($query->num_rows() > 0)
		{
			$row = $query-row();
			$parent_id  = $row->predecessor_id;
		}
		$query->free_result();
		return $parent_id;
	}
	/*
		Method:
			get_parent_articles()
			
		Returns an array of top level articles that do not reference any predecessors.
		
		Parameters:
			$storyline_id - Storyline ID int
			
		Return:
			Array of parent article IDs,
	
	*/
	private function get_parent_articles($storyline_id = false) 
	{
		$articles = array();
		$this->db->join('storylines_article_predecessors','storylines_article_predecessors.article_id = storylines_articles.id','left outer')
				 ->where('storylines_article_predecessors.article_id IS NULL')
				 ->where('storylines_articles.deleted',0);
		$articles = $this->select('storylines_articles.id, storylines_articles.storyline_id, subject, wait_days_min, wait_days_max, in_game_message, comments_thread_id, created_on, modified_on, deleted')->find_all_by('storylines_articles.storyline_id',$storyline_id);
		
		return $articles;
	}
	
	/*
		Method:
			get_article_children()
			
		Returns an array of articles that are children of the passed article_id.
		
		Parameters:
			$article_id - Storyline Article ID int
			
		Return:
			Array of article return objects,
	
	*/
	private function get_article_children_details($article_id = false) 
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received to find children.<br/>\n";
			return false;
		}
		$children = array();
		$str_ids = "(".implode(",",$this->get_article_children($article_id)).")";
		if ($str_ids != "()") 
		{
			$this->db->select('id, storyline_id, subject, wait_days_min, wait_days_max, in_game_message, comments_thread_id, created_on, modified_on, deleted')
					->where('deleted',0)
					->where_in('id',$str_ids);
			$child_results = $this->db->get($this->table);
			if ($child_results->num_rows() > 0)
			{			
				foreach ($child_results->result() as $child)
				{	
					$child->children = $this->get_article_children($child->id);
					array_push($children,$child);
				}
			}
			$child_results->free_result();
		}
		return $children;
	}
}