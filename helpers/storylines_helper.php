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
			foreach($articles as $article) {
			
				$link =anchor(SITE_AREA.'/custom/storylines/articles/edit/'.$article->id,$article->subject);
				$id = $article->id;
				$storyline_id = $article->storyline_id;
				$details = lang('sl_details');
				$edit = lang('sl_edit');
				$delete = lang('sl_delete');
				$html_out = <<<EOL
				<tr>
					<td>
						<input type="checkbox" name="checked[]" value="{$id}" />
					</td>
					<td>{$link}</td>
					<td>
						<a class="btn btn-small" href="#" rel="article_details" id="{$storyline_id}|{$id}">
							<i class=" icon-zoom-in"></i> {$details}
						</a>
						<a class="btn btn-small" href="#" rel="article_edit" id="{$storyline_id}|{$id}>">
							<i class="icon-edit"></i> ($edit)
						</a>
						<a class="btn btn-small" href="#" rel="article_remove" id="{$storyline_id}|{$id}">
							<i class=" icon-remove"></i> {$delete}
						</a>
					</td>
				</tr>
EOL;
				if (isset($article->children) && sizeof($article->children) > 0) 
				{
					$html_out .= draw_articles($article->children);
				}
			}
		}
		return $html_out;
	}
}
/* End of file storylines_helper.php */
/* Location: ./modules/storylines/helpers/storylines_helper.php */