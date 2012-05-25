<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Additonal_storyline_options extends Migration {

    private $permission_array = array(
        'Storylines.Settings.View' => 'Manage Storylines Settings.',
        'Storylines.Custom.View' => 'View Storylines menu item and index.'
    );
    public function up()
	{
		$prefix = $this->db->dbprefix;
	
		$default_settings = "
			INSERT INTO `{$prefix}settings` (`name`, `module`, `value`) VALUES
			 ('storylines.comments_enabled', 'storylines', '1');
		";
        $this->db->query($default_settings);
		
		$this->dbforge->add_column('storylines', array(
                'flagged'	=> array(
                'type'	=> 'int',
                'constraint'	=> 1,
                'default'		=> '0'
            )
        ));
        $this->dbforge->add_column('storylines', array(
                'flag_message'	=> array(
                'type'	=> 'varchar',
                'constraint'	=> 255,
                'default'		=> ''
            )
        ));
        foreach ($this->permission_array as $name => $description)
        {
            $this->db->query("INSERT INTO {$prefix}permissions(name, description) VALUES('".$name."', '".$description."')");
            // give current role (or administrators if fresh install) full right to manage permissions
            $this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1,".$this->db->insert_id().")");

        }
    }
	//--------------------------------------------------------------------
	
	public function down() 
	{
        $prefix = $this->db->dbprefix;

        // remove the keys
		$this->db->query("DELETE FROM {$prefix}settings WHERE (name = 'storylines.comments_enabled'
		)");
        $this->dbforge->drop_column("storylines","flagged");
        $this->dbforge->drop_column("storylines","flag_message");
		foreach ($this->permission_array as $name => $description)
        {
            $query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = '".$name."'");
            foreach ($query->result_array() as $row)
            {
                $permission_id = $row['permission_id'];
                $this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
            }
            //delete the role
            $this->db->query("DELETE FROM {$prefix}permissions WHERE (name = '".$name."')");
        }
    }
	
	//--------------------------------------------------------------------
	
}