<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Storylines_sync_updates extends Migration {

    public function up()
	{
		$prefix = $this->db->dbprefix;

        // NEW TRIGGERS
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(94,'team_wins_game',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(95,'team_loses_game',5000,'','',1)");
		
		// NEW CONDITIONS
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(191,'CDT_TEAM_LOSING_STREAK',0,9999,'team lost a certain number of games in a row', 1, 'team_losing_streak', '', 2, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(192,'CDT_TEAM_WINNING_STREAK',0,9999,'team won a certain number of games in a row', 1, 'team_winning_streak', '', 2, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(193,'CDT_PLAYER_IS_REAL_PERSON',0,1,'if the player has a Lahman ID, its a real person', 2, 'player_is_real_person', '', 2, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(194,'CDT_PLAYER_POTENTIAL_MIN',0,7,'', 1, 'player_potential_min', '', 2, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(195,'CDT_PLAYER_POTENTIAL_MAX',0,7,'', 1, 'player_potential_max', '', 2, 2, 1)");
		// RESULTS
		$this->db->query("UPDATE {$prefix}list_storylines_results SET value_range_max = '5' WHERE slug LIKE '%_modifier'");
		//TOKENS
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamwinningstreak','7', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlosingstreak','3', 3, 1)");
		
		$this->dbforge->add_column('storylines_data_objects', array(
                'main_actor'	=> array(
                'type'			=> 'int',
                'constraint'	=> 2,
                'default'		=> '0'
            )
        ));
		$this->db->query("UPDATE {$prefix}list_storylines_results SET value_range_min = '-5', value_range_max = '5' WHERE name LIKE '%_modifier' ");
		
		
    }
	//--------------------------------------------------------------------
	
	public function down() 
	{
        $prefix = $this->db->dbprefix;

		// REMOVE NEW DATA VALUES
		$this->db->query("DELETE FROM {$prefix}list_storylines_triggers WHERE id in(95,96)");
		$this->db->query("DELETE FROM {$prefix}list_storylines_conditions WHERE id in(191,192,193,194,195)");
		$this->db->query("DELETE FROM {$prefix}list_storylines_tokens WHERE slug in('teamwinningstreak','teamlosingstreak')");
		$this->db->query("UPDATE {$prefix}list_storylines_results value_range_max = '200' WHERE slug LIKE '%_modifier'");
		
		
    }
	//--------------------------------------------------------------------
	
}