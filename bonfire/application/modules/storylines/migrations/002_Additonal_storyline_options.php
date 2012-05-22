<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Additonal_storyline_options extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;
	
		$default_settings = "
			INSERT INTO `{$prefix}settings` (`name`, `module`, `value`) VALUES
			 ('storylines.comments_enabled', 'storylines', '1');
		";
        $this->db->query($default_settings);

    }
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
        $prefix = $this->db->dbprefix;

        // remove the keys
		$this->db->query("DELETE FROM {$prefix}settings WHERE (name = 'storylines.comments_enabled'
		)");

    }
	
	//--------------------------------------------------------------------
	
}