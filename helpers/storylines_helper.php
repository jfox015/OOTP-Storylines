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
function find_author_name ( $id = 0 )
{
	$ci =& get_instance();

	$ci->load->model('author_model');

	return $ci->author_model->find_author ($id);

}
/* End of file dataList_helper.php */
/* Location: ./system/helpers/dataList_helper.php */