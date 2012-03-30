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
		$articles = $this->get_article_parents($storyline_id);

		if (count($articles))
		{
			foreach ($articles as $article)
			{
				$article->children = $this->get_article_children($article->id);
			}
		}
		return $articles;
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
		$this->db->select('id, results_id, result_value')
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
		return $this->select('subject, wait_days_min, wait_days_max, in_game_message, comment_thread_id, created_on, modified_on, deleted')
					->find($article_id);
	}
	/*
		Method:
			get_article_parents()
			
		Returns an array of top level articles that do not reference any predecessors.
		
		Parameters:
			$storyline_id - Storyline ID int
			
		Return:
			Array of parent article IDs,
	
	*/
	private function get_article_parents($storyline_id = false) 
	{
		$articles = array();
		$this->db->join('storylines_article_predecessors','storylines_article_predecessors.article_id = storylines_articles.id','left outer')
				 ->where('storylines_article_predecessors.article_id IS NULL')
				 ->where('storylines_articles.deleted',0);
		$articles = $this->select('storylines_articles.id, subject, wait_days_min, wait_days_max, in_game_message, comments_thread_id, created_on, modified_on, deleted')->find_all_by('storylines_articles.storyline_id',$storyline_id);
		
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
	private function get_article_children($article_id = false) 
	{
		if ($article_id === false)
		{
			$this->error .= "No article ID was received to find children.<br/>\n";
			return false;
		}
		$children = array();
		$query = $this->db->select('article_id')
							 ->where('predecessor_id',$article_id)
							 ->get('storylines_article_predecessors');
							 
		if ($query->num_rows() > 0)
		{
			$str_ids = "(";
			foreach ($query->result() as $row)
			{
				if ($str_ids != "(") { $str_ids .= ","; }
				$str_ids .= $row->article_id;
			}
			$str_ids .= ")";
			$this->db->select('id, subject, wait_days_min, wait_days_max, in_game_message, comments_thread_id, created_on, modified_on, deleted')
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
		$query->free_result();
		return $children;
	}
}