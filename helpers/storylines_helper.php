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

		$ci->load->model('author_model');

		return $ci->author_model->find_author ($id);

	}
}
if (!function_exists('export_storylines'))
{
	function export_storylines($format = 'xml', $storylines = false)
	{
		return;
	}
}
if (!function_exists('make_spaces'))
{
	function make_spaces($count = 0)
	{
		$str_out = '';
		$i = $count;
		while ($i > 0)
		{
			$str_out .= "&nbsp;&nbsp;";
		}
		return $str_out;
	}
}
if (!function_exists('draw_articles'))
{
	function draw_articles($articles = false, $level = 1)
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
				$link_edit = site_url(SITE_AREA.'/custom/storylines/articles/edit/'.$article->id,$space.$level.".".$count." - ".$article->subject);
				$id = $article->id;
				$storyline_id = $article->storyline_id;
				$subject = $article->subject;
				$details = lang('sl_details');
				$edit = lang('sl_edit');
				$delete = lang('sl_delete');
				$html_out = <<<EOL
				<tr>
					<td>
						<input type="checkbox" name="checked[]" value="{$id}" />
					</td>
					<td>{$subject}</td>
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
					$html_out .= draw_articles($article->children, $level++);
				}
				$count++;
			}
		}
		return $html_out;
	}
}
/* End of file storylines_helper.php */
/* Location: ./modules/storylines/helpers/storylines_helper.php */