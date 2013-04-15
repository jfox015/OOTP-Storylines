<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Storylines Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jeff Fox
 * @version		1.0
 */
// ------------------------------------------------------------------------
if (!function_exists('find_author_name'))
{
	function find_author_name ( $id = 0 )
	{
		$ci =& get_instance();

		$ci->load->model('storylines/author_model');

		return $ci->author_model->find_author ($id);

	}
}// ------------------------------------------------------------------------
if (!function_exists('limit_text'))
{
	function limit_text($text, $limit) {
		$strings = $text;
		if (strlen($text) > $limit) 
		{
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			if(sizeof($pos) >$limit)
			{
				$text = substr($text, 0, $pos[$limit]) . '...';
			}
			return $text;
		}
		return $text;
    }
}
if (!function_exists('export_storylines'))
{
	function export_storylines($format = 'xml', $storylines = false)
	{
		$outArr = array('header'=>'','output'=>'','status'=>'');
		$strOut = '';
		$t = "\t";
		$n = "\n";
		if ($storylines === false || !is_array($storylines) || count($storylines) == 0)
		{
			$outArr['output'] = "No storyline data found to export";
			$outArr['status'] = "Warning";
			return $outArr;
		}
		switch ($format)
		{
			case 'json':
				$outArr['header'] = 'Content-type: application/json'; 
				$outArr['output'] = json_encode($storylines);
				$outArr['status'] = "success";
				break;
			
			case 'sql':
				
				foreach ($storylines as $storyline)
				{
					$strOut .= 'INSERT INTO storylines (name, description, random_frequency, category_id, publish_status_id, authoring_status_id, created_on, modified_on, created_by, modified_by) VALUES ('.
					'"'.addslashes(trim((string) $storyline->title)).'", "'.addslashes(trim((string)$storyline->description)).'", "'.$storyline->random_frequency.'", "'.$storyline->category_id.'", "'.$storyline->publish_status_id.'", "'.$storyline->author_status_id.
					'", "'.$storyline->created_on.'", "'.$storyline->modified_on.'", "'.$storyline->created_by.'", "'.$storyline->modified_by.');'.$n;
					$strOut .= 'SET @new_sl_id = LAST_INSERT_ID();'.$n;
					
					// add trigger events to storyline
					if (isset($storyline->triggers) && is_array($storyline->triggers) && count($storyline->triggers))
					{
						foreach ($storyline->triggers as $trigger)
						{
							$strOut .= 'INSERT INTO storylines_triggers (id, storyline_id, trigger_id) VALUES (0, @new_sl_id, '.$trigger->id.'];'.$n;
						}
					}	
					// add conditions to storyline
					if (isset($storyline->conditions) && is_array($storyline->conditions) && count($storyline->conditions))
					{
						foreach ($storyline->conditions as $condition)
						{
							$strOut .= 'INSERT INTO storylines_conditions (id, var_id, level_type, condition_id, value) VALUES (0, @new_sl_id, 1, '.$condition->id.', '.$condition->value.');'.$n;
						}
					}
					// REQUIRED DATA OBJECTS
					if (isset($storyline->data_objects) && is_array($storyline->data_objects) && count($storyline->data_objects))
					{
						foreach ($storyline->data_objects as $data_object)
						{
							$strOut .= 'INSERT INTO storylines_data_objects (id, storyline_id, object_id, object_num) VALUES (0, @new_sl_id, '.$data_object->id.', '.$data_object->object_num.');'.$n;
							$strOut .= 'SET @new_do_id = LAST_INSERT_ID();'.$n;

							// add conditions to object
							if (isset($data_object->conditions) && is_array($data_object->conditions) && count($data_object->conditions))
							{
								foreach ($data_object->conditions as $condition)
								{
									$strOut .= 'INSERT INTO storylines_conditions (id, var_id, level_type, condition_id, value) VALUES (0, @new_do_id, 3, '.$condition->id.', '.$condition->value.');'.$n;
								}
							}
							
						}
					}
					// ARTICLES
					if (isset($storyline->articles) && is_array($storyline->articles) && count($storyline->articles))
					{
						foreach ($storyline->articles as $article)
						{
							// ADD ARTICLE SQL
							$strOut .= 'INSERT INTO storylines_articles (id, storyline_id, subject, text, wait_days_min, wait_days_max, in_game_message, reply,'.
							'comments_thread_id, created_on, modified_on, created_by, modified_by, deleted) VALUES (0, @new_sl_id, '.$article->subject.','.
							addslashes(trim((string)$article->text)).','.$article->wait_days_min.','.$article->wait_days_max.','.$article->in_game_message.','.$article->reply.','
							.$article->comments_thread_id.','.$article->created_on.','.$article->modified_on.','.$article->created_by.','.$article->modified_by.','.$article->deleted.');'.$n;
							$strOut .= 'SET @new_art_id = LAST_INSERT_ID();'.$n;
							
							// add previous ids to article
							if (isset($article->predecessors) && is_array($article->predecessors) && count($article->predecessors))
							{
								foreach ($article->predecessors as $predecessor)
								{
									$strOut .= 'INSERT INTO storylines_article_predecessors (id, storyline_id, article_id, predecessor_id) VALUES (0, @new_sl_id, @new_art_id, '.$predecessor.');'.$n;
								}
							}
							// add conditions to article
							if (isset($data_object->conditions) && is_array($data_object->conditions) && count($data_object->conditions))
							{
								foreach ($data_object->conditions as $condition)
								{
									$strOut .= 'INSERT INTO storylines_conditions (id, var_id, level_type, condition_id, value) VALUES (0, @new_art_id, 2, '.$condition->id.', '.$condition->value.');'.$n;
								}
							}
							// add results to article
							if (isset($data_object->results) && is_array($data_object->results) && count($data_object->results))
							{
								foreach ($data_object->results as $result)
								{
									$strOut .= 'INSERT INTO storylines_results (id, storyline_id, article_id, result_id, value) VALUES (0, @new_sl_id, @new_art_id, '.$result->id.', '.$result->value.');'.$n;
								}
							}
						}
					}
				}
				$outArr['header'] = 'Content-type: text/sql'; 
				$outArr['output'] = $strOut;
				$outArr['status'] = "success";
				break;

			case 'xml':
			default:
				$strOut .= '<?xml version="1.0" encoding="ISO-8859-1"?>'.$n.$n;
				$strOut .= '<STORYLINE_DATABASE xmlver="1.3 Beta" fileversion="OOTPDev '.date('Y-m-d H:i:s').'" generator="SLE Community 0.1">'.$n.$n;
				$strOut .= $t.'<STORYLINES>'.$n;
				foreach ($storylines as $storyline)
				{
					$strOut .= $t.$t.'<STORYLINE id="'.htmlspecialchars($storyline->id).'" random_frequency="'.$storyline->random_frequency.'"';
					// add trigger events to storyline
					$triggerStr = '';
					if (isset($storyline->triggers) && is_array($storyline->triggers) && count($storyline->triggers))
					{
						foreach ($storyline->triggers as $trigger)
						{
							if (!empty($triggerStr)) $triggerStr .= ",";
							$triggerStr .= $trigger->slug;
						}
					}
					if (!empty($triggerStr)) $strOut .= ' trigger_events="'.$triggerStr.'"';
					
					// add conditions to storyline
					if (isset($storyline->conditions) && is_array($storyline->conditions) && count($storyline->conditions))
					{
						foreach ($storyline->conditions as $condition)
						{
							$strOut .= ' '.$condition->slug.'="'.$condition->value.'"';
						}
					}
					// close storyline tag
					$strOut .= '>'.$n;
					
					// REQUIRED DATA OBJECTS
					if (isset($storyline->data_objects) && is_array($storyline->data_objects) && count($storyline->data_objects))
					{
						$strOut .= $t.$t.$t.'<REQUIRED_DATA>'.$n;
						foreach ($storyline->data_objects as $data_object)
						{
							$strOut .= $t.$t.$t.$t.'<DATA_OBJECT type="'.$data_object->name.'"';
							// add conditions to object
							if (isset($data_object->conditions) && is_array($data_object->conditions) && count($data_object->conditions))
							{
								foreach ($data_object->conditions as $condition)
								{
									$strOut .= ' '.$condition->slug.'="'.$condition->value.'"';
								}
							}
							$strOut .= '>'.$n;
							$strOut .= $t.$t.$t.$t.'</DATA_OBJECT>'.$n;
						}
						$strOut .= $t.$t.$t.'</REQUIRED_DATA>'.$n;
					}
					
					// ARTICLES
					if (isset($storyline->articles) && is_array($storyline->articles) && count($storyline->articles))
					{
						$strOut .= $t.$t.$t.'<ARTICLES>'.$n;
						foreach ($storyline->articles as $article)
						{
							$strOut .= $t.$t.$t.$t.'<ARTICLE type="'.$article->id.'"';
							// add previous ids to article
							$predecessorStr = '';
							if (isset($article->predecessors) && is_array($article->predecessors) && count($article->predecessors) > 0)
							{
								foreach ($article->predecessors as $predecessor)
								{
									if (!empty($predecessorStr)) $predecessorStr .= ",";
									$predecessorStr .= $predecessor;
								}
							}
							if (!empty($predecessorStr)) $strOut .= ' previous_ids="'.$predecessorStr.'"';
					
							if (isset($article->wait_days_min) && intval($article->wait_days_min) > 0) 		$strOut .= ' wait_days_min="'.$article->wait_days_min.'"';
							if (isset($article->wait_days_max) && intval($article->wait_days_max) > 0) 		$strOut .= ' wait_days_max="'.$article->wait_days_max.'"';
							if (isset($article->in_game_message) && intval($article->in_game_message) > 0) 	$strOut .= ' in_game_message="'.$article->in_game_message.'"';
							
							// add conditions to article
							if (isset($article->conditions) && is_array($article->conditions) && count($article->conditions))
							{
								foreach ($article->conditions as $condition)
								{
									$strOut .= ' '.$condition->name.'="'.$condition->value.'"';
								}
							}
							
							// add results to article
							$inj_desc = '';
							if (isset($article->results) && is_array($article->results) && count($article->results))
							{
								foreach ($article->results as $result)
								{
									if ($result->slug != 'injury_description')
									{	
										$strOut .= ' '.$result->slug.'="'.$result->value.'"';
									}
									else
									{
										$inj_desc = $result->value;
									}
								}
							}
							$strOut .= '>'.$n;
							
							// add misc data to article
							$strOut .= $t.$t.$t.$t.$t.'<SUBJECT>'.htmlspecialchars(trim($article->subject)).'</SUBJECT>'.$n;
							$strOut .= (isset($article->text) ? $t.$t.$t.$t.$t.'<TEXT>'.htmlspecialchars(trim($article->text)).'</TEXT>'.$n : '');
							if (isset($article->reply) && !empty($article->reply)) 
							{
								$strOut .= $t.$t.$t.$t.$t.'<REPLY>'.htmlspecialchars(trim($article->reply)).'</REPLY>'.$n;
							}
							if (!empty($inj_desc)) 
							{
								$strOut .= $t.$t.$t.$t.$t.'<INJURY_DESCRIPTION>'.htmlspecialchars(trim($inj_desc)).'</INJURY_DESCRIPTION>'.$n;
							}
							
							$strOut .= $t.$t.$t.$t.'</ARTICLE>'.$n;
						}
						$strOut .= $t.$t.$t.'</ARTICLES>'.$n;
					}
					// close storyline block
					$strOut .= $t.$t.'</STORYLINE>'.$n.$n;
				}
				$strOut .= "\t".'</STORYLINES>'."\n";
				$strOut .= '</STORYLINE_DATABASE>'."\n";
				
				$outArr['header'] = 'Content-type: text/xml'; 
				$outArr['output'] = $strOut;
				$outArr['status'] = "success";
				break;
		}
		return $outArr;
	}
}
if (!function_exists('make_spaces'))
{
	function make_spaces($count = 0)
	{
		$str_out = '';
		$i = 1;
		while ($i < $count)
		{
			$str_out .= "&nbsp;---&nbsp;";
			$i++;
		}
		return $str_out;
	}
}
if (!function_exists('draw_articles'))
{
	function draw_articles($articles = false, $parent = 1, $level = 1)
	{
		if ($articles === false)
		{
			return false;
		}
		$html_out = '';
		if (is_array($articles) && sizeof($articles) > 0)
		{
			$count = 1;
			foreach($articles as $article) {
			
				$space = '';
				if ($level > 1) { $space = make_spaces($level); }
				$link_edit = site_url(SITE_AREA.'/custom/storylines/articles/edit/'.$article->id);
				$id = $article->id;
				$title = $space.$parent.'.'.$level;
				if ($level > 1) { $title .= '.'.$count; }
				$title .= ' - '.(isset($article->title) ? $article->title : $article->subject);
				$edit = lang('sl_edit');
				$delete = lang('sl_delete');
				$icon_class = '';
				$icon_alt = '';
				switch ($article->in_game_message)
				{
					case 1: // LEAGUE NEWS
						$icon_class  = 'icon-list-alt';
						$icon_alt = "League News";
						break;
					case 2: // PERSONAL MESSAGE
						$icon_class  = 'icon-inbox';
						$icon_alt = "Personal Message";
						break;
					case 3: // NO MESSAGE (Replies)
						$icon_class  = 'icon-remove';
						$icon_alt = "No Message";
						break;
					
				}				
				$html_out .= <<<EOL
				<tr>
					<td><i class="{$icon_class}" rel="tooltip" data-original-title="{$icon_alt}"></i></td>
					<td>{$title}</td>
					<td>
						<a class="btn btn-small" href="{$link_edit}">
							<i class="icon-edit"></i> ($edit)
						</a>
						<a class="btn btn-small" href="#" rel="delete_article" id="{$id}">
							<i class=" icon-remove"></i> {$delete}
						</a>
					</td>
				</tr>
EOL;
				if (isset($article->children) && is_array($article->children) && sizeof($article->children) > 0)
				{
					$new_level = $level + 1;
					$html_out .= draw_articles($article->children, $parent, $new_level);
				}
				if ($level == 1) $parent = $parent + 1;
				$count = $count + 1;
			}
		}
		return $html_out;
	}
}
/* End of file storylines_helper.php */
/* Location: ./modules/storylines/helpers/storylines_helper.php */