<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_storylines extends Migration {
	
	public function up() 
	{
		$prefix = $this->db->dbprefix;
		
		$data = array(
			'name'        => 'Storylines.Settings.Manage' ,
			'description' => 'Manage Storylines Settings'
		);
		$this->db->insert("{$prefix}permissions", $data);
		
		$permission_id = $this->db->insert_id();
		
		$this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1, ".$permission_id.")");

		$data = array(
			'name'        => 'Storylines.Content.Add' ,
			'description' => 'Add Storylines Content'
		);
		$this->db->insert("{$prefix}permissions", $data);

		$permission_id = $this->db->insert_id();

		$this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1, ".$permission_id.")");

		$data = array(
			'name'        => 'Storylines.Content.Manage' ,
			'description' => 'Manage Storylines Content'
		);
		$this->db->insert("{$prefix}permissions", $data);

		$permission_id = $this->db->insert_id();

		$this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1, ".$permission_id.")");

		$data = array(
			'name'        => 'Storylines.Data.Manage' ,
			'description' => 'Manage Storylines Data Values'
		);
		$this->db->insert("{$prefix}permissions", $data);

		$permission_id = $this->db->insert_id();

		$this->db->query("INSERT INTO {$prefix}role_permissions VALUES(1, ".$permission_id.")");

		// ADD TABLES
		
		// Storylines
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field('`title` varchar(255) NOT NULL');
		$this->dbforge->add_field('`description` LONGTEXT NOT NULL');
		$this->dbforge->add_field("`tags` varchar(255) NOT NULL");
		
		$this->dbforge->add_field("`author_status_id` tinyint(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`publish_status_id` tinyint(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`category_id` tinyint(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`comments_thread_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`random_frequency` int(11) NOT NULL DEFAULT '0'");

		$this->dbforge->add_field("`created_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`created_by` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`modified_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`modified_by` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`deleted` tinyint(1) NOT NULL DEFAULT '0'");
		
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines');
		
		// Storylines Articles
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`storyline_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field('`title` varchar(255) NOT NULL');
		$this->dbforge->add_field('`description` LONGTEXT NOT NULL');
		$this->dbforge->add_field("`subject` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`text` LONGTEXT NOT NULL");
		
		$this->dbforge->add_field("`wait_days_min` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`wait_days_max` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`in_game_message` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`reply` varchar(255) NOT NULL");
		
		$this->dbforge->add_field("`comments_thread_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`created_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`created_by` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`modified_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`modified_by` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`deleted` tinyint(1) NOT NULL DEFAULT '0'");
		
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_articles');
		 
		// Storylines Triggers
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`storyline_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`trigger_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_triggers');
		
		// Storylines Data Objects
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`storyline_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`object_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`object_num` int(4) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_data_objects');
		
		// Storylines Conditions
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`var_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`level_type` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`condition_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`value` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_conditions');
		
		// Storylines History
		$this->dbforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT");
		$this->dbforge->add_field("`var_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`object_type` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`added` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`removed` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`modified` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`created_on` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`created_by` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_history');
		
		// Storylines Articles Results
		$this->dbforge->add_field("`id` int(11) NOT NULL AUTO_INCREMENT");
		$this->dbforge->add_field("`storyline_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`article_id` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`result_id` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`result_value` varchar(325) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_article_results');
		
		// Storylines Articles Predecessors
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`storyline_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`article_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`predecessor_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('storylines_article_predecessors');
		
		// STORYLINE 1
		$comments = $this->db->table_exists('comments_threads');
		$comments_thread_id = 0;
		if ($comments) { 
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0,'storylines')");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0,1)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines VALUES(0, 'Test Story','<b>This is a test</b><br />Testing how this all works out.</b>','news,article,first',1,1,1,{$comments_thread_id},5000,".time().",1,".time().",1,0)");
        $storyline_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$storyline_id}, 1,'Added default storyline and article','','',".time().",1)");


		// ARTICLE 1
		if ($comments) {
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0,'storylines')");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0,1)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines_articles VALUES(0, {$storyline_id}, 'Test Article','This is a test. Testing how this all works out.','Pujols Spains akle playing catach','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lobortis ullamcorper bibendum. Vivamus mauris dolor, hendrerit nec sodales et, pulvinar vel neque. Vestibulum sit amet nibh lacus.',0,0,1,'',{$comments_thread_id},".time().",2,".time().",1,0)");
		$prev_article_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$prev_article_id}, 2,'Added article to storlyine','','',".time().",1)");
		
		// ARTICLE 2
		if ($comments) {
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0,'storylines')");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0,1)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines_articles VALUES(0, {$storyline_id}, 'Child Test Article 1','This is a child test. Testing how this all works out.','Pujols slams thumb,. Says good gravy!','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lobortis ullamcorper bibendum. Vivamus mauris dolor, hendrerit nec sodales et, pulvinar vel neque. Vestibulum sit amet nibh lacus.',7,14,1,'',{$comments_thread_id},".time().",3,".time().",1,0)");
		$article_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$article_id}, 2,'Added article to storlyine','','',".time().",1)");
		$this->db->query("INSERT INTO {$prefix}storylines_article_predecessors VALUES(0, {$storyline_id}, {$article_id}, {$prev_article_id})");
		$prev_article_id = $article_id;
		
		// ARTICLE 3
		if ($comments) {
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0,'storylines')");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0,1)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines_articles VALUES(0, {$storyline_id}, 'A second level child test Article','This is a child test. Testing how this all works out.','Pujols wants to go see his mommy over brusied thumb.!','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lobortis ullamcorper bibendum. Vivamus mauris dolor, hendrerit nec sodales et, pulvinar vel neque. Vestibulum sit amet nibh lacus.',7,21,1,'',{$comments_thread_id},".time().",1,".time().",1,0)");
		$article_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$article_id}, 2,'Added article to storlyine','','',".time().",1)");
		$this->db->query("INSERT INTO {$prefix}storylines_article_predecessors VALUES(0, {$storyline_id}, {$article_id}, {$prev_article_id})");

		/*--------------------------
		/	LISTS
		/-------------------------*/
		// Storylines Random Frequencies
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`value` int(4) NOT NULL DEFAULT '0'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_random_frequencies');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Once in a generation',100)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Extremely rare',1000)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Rarely',2000)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Standard',5000)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Often',6500)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Very Often',8000)");
		
		// Storylines Article message types List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_articles_message_types');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_articles_message_types VALUES(1,'League News')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_articles_message_types VALUES(2,'Personal Message')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_articles_message_types VALUES(3,'No Message')");
		
		// Storylines Triggers List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`random_frequency` int(11) NOT NULL DEFAULT '5000'");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`active` int(1) NOT NULL DEFAULT '1'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_triggers');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(1,'allstar_announcement',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(2,'allstar_game',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(3,'arbitration_hearings',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(4,'batter_of_month',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(5,'batter_of_year',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(6,'expansion_draft',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(7,'fantasy_draft',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(8,'fielder_of_year',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(9,'free_agents_file',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(10,'good_player_files_for_fa',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(11,'manager_of_year',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(12,'offseason_start',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(13,'pitcher_of_month',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(14,'pitcher_of_year',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(15,'player_asks_for_extension',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(16,'player_demands_trade',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(17,'player_of_week',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(18,'player_unhappy_with_role',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(19,'playoff_start',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(20,'preseason_start',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(21,'protection_lists',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(22,'rookie_draft',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(23,'rookie_draft_deadline',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(24,'rookie_draft_pool',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(25,'rookie_of_month',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(26,'rookie_of_year',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(27,'roster_expansion',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(28,'rule_5_draft',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(29,'season_ends',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(30,'season_start',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(31,'spring_start',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(32,'team_wins_championship',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(33,'trading_deadline',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(34,'two_players_brawl',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(35,'winter_meetings_ends',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(36,'winter_meetings_start',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(37,'player_announces_retirement',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(38,'hall_of_fame_announcement',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(39,'player_files_for_fa ',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(40,'player_morale_decreases_to_unhappy',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(41,'player_morale_decreases_to_angry',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(42,'playoff_ends',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(43,'free_agent_player_signs_contract',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(44,'player_signs_extension',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(45,'player_retires',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(46,'player_traded',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(47,'player_drafted',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(49,'player_placed_on_waivers',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(50,'player_claimed_off_waivers',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(51,'player_released',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(55,'player_suspended',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(56,'player_commits_multiple_errors_in_game',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(57,'player_issues_over_5_walks_in_game',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(58,'player_reaches_every_1000th_hit_starting_at_2000',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(59,'player_reaches_every_100th_hr_starting_at_300',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(60,'player_reaches_every_1000th_rbi_starting_at_1000',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(61,'player_reaches_every_100th_sb_starting_at_500',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(62,'player_reaches_every_100th_w_starting_at_200',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(63,'player_reaches_every_1000th_k_starting_at_2000',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(64,'player_reaches_every_100th_s_starting_at_400',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(65,'manager_hired',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(66,'manager_fired',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(67,'manager_resigns',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(68,'manager_retires',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(69,'coach_hired',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(70,'coach_fired',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(71,'coach_resigns',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(72,'coach_retires',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(73,'retired_player_becomes_coach',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(74,'retired_player_becomes_manager',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(75,'(inactive, do not use)',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(76,'(inactive, do not use)',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(78,'league_announces_expansion',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(79,'team_relocates',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(80,'team_changes_name',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(87,'team_market_size_increases',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(88,'team_market_size_decreases',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(89,'team_fan_loyalty_increases',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(90,'team_fan_loyalty_decreases',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(91,'team_eliminated_from_postseason_contention',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(92,'team_clinches_postseason_berth',5000,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_triggers VALUES(93,'team_clinches_division_title',5000,'','',1)");

		// Storylines Data Objects List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`active` int(1) NOT NULL DEFAULT '1'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_data_objects');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(1,'LEAGUE', 'League','Not necessary. Always added by default.',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(2,'PLAYER', 'Player','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(3,'TEAM_MATE', 'Teammate','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(4,'NON_TEAM_MATE', 'Non Teammate','Team Bench Coach',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(5,'OWNER', 'Team Owner','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(6,'MANAGER', 'Manager','Team Bench Coach',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(7,'PITCHING_COACH', 'Pitching Coach','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(8,'HITTING_COACH', 'Hitting Coach','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(9,'BENCH_COACH', 'Bench Coach','Team Bench Coach',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(10,'HEAD_SCOUT', 'Head Scout','Teams Head Scout',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(11,'DOCTOR', 'Doctor','Team Trainer/Doctor',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(12,'TEAM', 'Team','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(13,'OTHER_TEAM', 'Other Team','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(14,'OTHER_LEAGUE', 'Other League','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(15,'LOWER_LEAGUE', 'Minor League (Any)','Team Bench Coach',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(16,'HIGHER_LEAGUE', 'Higher League','Available for Minors/feeders only',1)");
		
		// Storylines categories List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_categories');

		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(1,'default', 'Default')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(2,'Events - Clubhouse','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(3,'Events - In Game','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(4,'Events - League','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(5,'Events - Off-field','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(6,'Events - Rosters','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(7,'Events - Team','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(8,'Events - Transactions','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(9,'Hall of Fame','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(10,'Injuries - Career Ending','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(11,'Injuries - In Game','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(12,'Injuries - Off Field','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(13,'Milestones','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(14,'Players - Minors','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(15,'Players - Minors','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(16,'Players - Retired','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(17,'Records','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(18,'Season - Pre-season','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(19,'Season - Regular Season','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(20,'Season - Draft','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(21,'Season - All Star Game','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(22,'Season - Pennant races','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(23,'Season - Playoffs','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(24,'Season - Post-season','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(25,'Streaks - Players','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(26,'Streaks - Teams','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(27,'Charity - All', 'Charity Stories')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(28,'Personel - Coach Related','coach')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(29,'Controversey', 'controversey')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(30,'Death - All', 'death')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(31,'Family', 'family')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(32,'Funny/Humor', 'funny')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(33,'Personel - Manager Related', 'manager')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(34,'Personel - Owner Related', 'owner')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(35,'Personel - Trainer Related', 'owner')");

		// Storylines review status List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_author_status');

		$this->db->query("INSERT INTO {$prefix}list_storylines_author_status VALUES(-1, 'unknown', 'Unknown')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_author_status VALUES(1, 'open', 'Open for Contributions')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_author_status VALUES(2, 'locked', 'Locked')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_author_status VALUES(3, 'add', 'Add Articles')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_author_status VALUES(4, 'correct', 'Corrections Only')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_author_status VALUES(5, 'change', 'Changes Needed. See Comments.')");

		// Storylines publish status List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_publish_status');

		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(-1, 'unknown', 'Unknown')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(1, 'added', 'Added')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(2, 'review', 'In Review')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(3, 'approved', 'Approved for Publish')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(4, 'rejected', 'Rejected')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(5, 'archived', 'Archived')");

		// Storylines Conditions List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`value_range_min` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`value_range_max` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`type_id` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`options` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`level_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`category_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`active` int(1) NOT NULL DEFAULT '1'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_conditions');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(1,'ONLY_IN_SEASON',1,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(2,'ONLY_IN_OFFSEASON',1,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(3,'ONLY_IN_SPRING',1,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(4,'LEAGUE_YEAR_MIN',0,9999,'', 1, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(5,'LEAGUE_YEAR_MAX',0,9999,'', 1, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(6,'LEAGUE_YEAR',0,9999,'', 1, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(7,'WORLD_YEAR_MIN',0,9999,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(8,'WORLD_YEAR_MAX',0,9999,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(9,'WORLD_YEAR',0,9999,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(10,'WORLD_MONTH_MIN',0,12,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(11,'WORLD_MONTH_MAX',0,12,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(12,'WORLD_MONTH',0,12,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(13,'WORLD_DATE_MIN',0,31,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(14,'WORLD_DATE_MAX',0,31,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(15,'WORLD_DATE',0,31,'', 1, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(16,'IS_MINOR_LEAGUE',0,1,'yes/no', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(17,'LEAGUE_LEVEL',1,10,'Select a level(Major, minors, etc)', 3, 'League Level', '1:majors|2:triple_a|3:double_a|4:single_a|5:short_season_single_a|6:rookie|7:international|8:winter_league|9:college|10:high_school', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(18,'PERSON_AGE_MIN',0,99,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(19,'PERSON_AGE_MAX',0,99,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(20,'PERSON_GREED_MIN',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(21,'PERSON_GREED_MAX',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(22,'PERSON_LOYALTY_MIN',0,200,'Player X must have Loyalty rating is at least/below X (on the 1-200 scale)', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(23,'PERSON_LOYALTY_MAX',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(24,'PLAYER_LEADERSHIP_MIN',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(25,'PLAYER_LEADERSHIP_MAX',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(26,'PLAYER_MORALE_MAX',0,200,'player on active roster has a morale of Angry', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(27,'PLAYER_MORALE_MIN',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(28,'PLAYER_IN_FEEDER',0,1,'no/yes', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(29,'PLAYER_DRAFT_ELIGIBLE',0,1,'no/yes', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(30,'PLAYER_LAST_CONTRACT_YEAR',0,1,'no/yes', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(31,'PLAYER_ON_ACTIVE',0,1,'Player X and Player Y are both on the active roster.', 2, 'On Team Active Roster', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(32,'PLAYER_ON_SECONDARY',0,1,'Player X must be on Team Ys secondary roster', 2, 'On Team Secondary (40 man) Roster', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(33,'PLAYER_CONTRACT_LEFT_MIN',0,99,'Player X must have at least one additional non-optional year remaining on his contract', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(34,'PLAYER_MLB_SERVICE_MIN',0,99,'Player X has at least X years of major league service time', 0, 'Service Time - Minimum', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(35,'PLAYER_MLB_SERVICE_MAX',0,99,'Player X has no more than X years of major league service time', 1, 'Service Time - Maximum', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(36,'PLAYER_SEASON_PERFORMANCE_MIN',0,25,'players VORP is not in the top 5 of players on active roster', 1, 'Players Season Performance Min (modifier)', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(37,'PLAYER_SEASON_PERFORMANCE_MAX',0,25,'Players Season Performance Max (modifier)', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(38,'PLAYER_FA_ELIGIBLE',0,1,'no/yes, player X qualifies for free agency', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(39,'PLAYER_FA',0,1,'Player X is a free agent', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(40,'PLAYER_IS_UPCOMING_FA',0,1,'Player X is a pending free agent and hasnt signed an extension', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(41,'PLAYER_WORK_ETHIC_MIN',0,200,'Player Xs Work Ethic rating is at least/below X (on 1-200 scale)', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(42,'PLAYER_WORK_ETHIC_MAX',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(43,'PLAYER_INTELLIGENCE_MIN',0,200,'Player Xs Intelligence rating is at least/below X (on 1-200 scale)', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(44,'PLAYER_INTELLIGENCE_MAX',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(45,'PLAYER_IP_MIN',0,999,'Player X accumulated at least 50 IP in the previous season', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(46,'PLAYER_IP_MAX',0,999,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(47,'PLAYER_AB_MIN',0,999,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(48,'PLAYER_AB_MAX',0,999,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(49,'PLAYER_DESIRE_FOR_WINNER_MIN',0,200,'Player Xs Desire for Winner rating is at least/below X (on 1-200 scale)', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(50,'PLAYER_DESIRE_FOR_WINNER_MAX',0,200,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(51,'PLAYER_FOREIGN',0,1,'Player X has a nationality that is different/same as than the parent leagues nation', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(52,'PLAYER_NATIONALITY_ID',0,9999,'', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(53,'PLAYER_QUALITY_MIN',0,7,'sum of players Contact, Gap, Power and Eye/Patience actual ratings (for hitters) or players Fastball, Stuff, Movement and Control actual ratings (for pitchers) is at least/below X', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(54,'PLAYER_QUALITY_MAX',0,7,'Sum of players Contact, Gap, Power and Eye/Patience actual ratings (for hitters) or players Fastball, Stuff, Movement and Control actual ratings (for pitchers) is at least/below X', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(55,'PLAYER_PRO_SERVICE_MIN',0,99,'player has accrued no professional service time', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(56,'PLAYER_PRO_SERVICE_MAX',0,99,'Professional service time', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(57,'PLAYER_BMI_MIN',0,99,'Player X is at least 200 lbs. (if below 183 cm in height), or at least 220 lbs. (if between 183-198 cm in height)', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(58,'PLAYER_BMI_MAX',0,99,'Body Mass Index', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(59,'PLAYER_IN_MINORS',0,1,'Player X is on a major or minor league roster within Team Ys organization', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(60,'PLAYER_POSITION',1,16,'Select the player position', 3, 'Player Position', '1:pitcher|2:catcher|3:first_base|4:second_base|5:third_base|6:shortstop|7:left_field|8:center_field|9:right_field|10:designated_hitter|11:starter|12:reliever|13:closer|14:all_batters|15:highschool_players|16:college_players', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(61,'COACH_HANDLE_VETERANS_MIN',0,200,'Manager has a Handle Veterans rating of at least/below X or higher (on the 1-200 scale)', 1, 'Coach Handles Veterans - Minimum Value', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(62,'COACH_HANDLE_VETERANS_MAX',0,200,'', 1, 'Coach Handles Veterans - Maximum Value', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(63,'COACH_HANDLE_ROOKIES_MIN',0,200,'Manager has a Handle Rookies rating of at least X (on the 1-200 scale)', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(64,'COACH_HANDLE_ROOKIES_MAX',0,200,'Manager has a Handle Rookies rating of no more than X (on the 1-200 scale)', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(65,'COACH_YEARS_WITH_TEAM_MIN',0,99,'Team Y Scouting Director has been with the team for over 1 year', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(66,'COACH_YEARS_WITH_TEAM_MAX',0,99,'', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(67,'COACH_HANDLE_PLAYERS_MIN',0,200,'Hitting/Pitching Coach (depending on which portion of Condition #1 was met) has a Handle Players rating of at least/below X (on 1-200 scale)', 0, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(68,'COACH_HANDLE_PLAYERS_MAX',0,200,'', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(69,'TEAM_RECORD_MIN',0,1000,'Team Y is under .500 (enter 500, minimum 20 games played, applicable for in-season narrative)', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(70,'TEAM_RECORD_MAX',0,1000,'Team Y is over .500 (enter 500, minimum 20 games played, applicable for in-season narrative)', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(71,'TEAM_IN_PLAYOFFS',0,1,'Team Y failed to make the playoffs in the previous season (applicable for out-of-season-narrative)', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(72,'TEAM_LOSING_STREAK_MIN',0,9999,'team_winning_streak_min team has lost 5 games in a row OR has lost 10 of last 15 games', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(73,'TEAM_WINNING_STREAK_MIN',0,9999,'', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(74,'TEAM_MONEY_AVAILABLE',0,1,'Team Y has available $ for free agents', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(75,'OWNER_PATIENCE_MIN',0,10,'Owner X has a Patience rating of at least/below X (on the 1-10 scale)', 2, '', '', 1, 7, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(76,'OWNER_PATIENCE_MAX',0,10,'', 1, '', '', 1, 7, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(77,'OWNER_FISCAL_MIN',0,10,'', 1, '', '', 1, 7, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(78,'OWNER_FISCAL_MAX',0,10,'', 1, '', '', 1, 7, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(79,'STORYLINE_HAPPENS_ONLY_ONCE',0,1,'', 2, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(80,'MIN_USAGE_INTERVAL_DAYS',0,1,'', 2, '', '', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(81,'WORLD_DAY_OF_WEEK',1,7,'Sunday is 1', 3, 'Day of the Week', '1:Sunday|2:Monday|3:Tuesday|4:Wednesday|5:Thursday|6:Friday|7:Saturday', 1, 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(82,'PERSON_BMI_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(83,'PERSON_BMI_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(84,'PERSON_FOREIGN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(85,'PERSON_NATION_ID',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(86,'PERSON_STATE_ID',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(87,'PERSON_HOMETOWN_DISTANCE_FROM_TEAM_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(88,'PERSON_HOMETOWN_DISTANCE_FROM_TEAM_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(89,'(inactive, do not use)',0,1,'', 2, '', '', 1, 1, 0)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(90,'(inactive, do not use)',0,1,'', 2, '', '', 1, 1, 0)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(91,'LEAGUE_NATION_ID',0,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(92,'LEAGUE_DAYS_AFTER_ALLSTAR_GAME_MIN',0,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(93,'LEAGUE_DAYS_AFTER_ALLSTAR_GAME_MAX',0,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(94,'LEAGUE_DAYS_UNTIL_TRADING_DEADLINE_MIN',0,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(95,'LEAGUE_DAYS_UNTIL_TRADING_DEADLINE_MAX',0,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(96,'LEAGUE_PCT_SCHEDULE_COMPLETED_MIN',0,1,'', 2, '', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(97,'LEAGUE_PCT_SCHEDULE_COMPLETED_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(98,'PLAYER_PERSONALITY_IS_CANCER',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(99,'PLAYER_PERSONALITY_IS_CAPTAIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(100,'PLAYER_PERSONALITY_IS_FAN_FAVORITE',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(101,'PLAYER_PERSONALITY_IS_HUMBLE',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(102,'PLAYER_PERSONALITY_IS_SELFISH',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(103,'PLAYER_LOCAL_POPULARITY_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(104,'PLAYER_LOCAL_POPULARITY_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(105,'PLAYER_NATIONAL_POPULARITY_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(106,'PLAYER_NATIONAL_POPULARITY_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(107,'PLAYER_INJURY_PRONENESS_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(108,'PLAYER_INJURY_PRONENESS_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(109,'PLAYER_IS_INJURED',0,1,'Check if the player should be currently injured', 2, 'Player has existing injury', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(110,'PLAYER_HAS_CEI',0,1,'', 2, 'Player has career ending injury', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(111,'PLAYER_HAS_NTC',0,1,'', 2, 'Player has a no-trade clause', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(112,'PLAYER_IS_RETIRED',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(113,'PLAYER_IS_HALL_OF_FAMER',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(114,'PLAYER_IS_LEFTHANDED',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(115,'PLAYER_ATTENDED_COLLEGE',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(116,'PLAYER_IS_ON_MAJOR_LEAGUE_TEAM',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(117,'PLAYER_IS_ON_WAIVERS',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(118,'PLAYER_IS_DFA',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(119,'PLAYER_IS_ON_TRADE_BLOCK',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(120,'PLAYER_IS_ON_UNTOUCHABLE_LIST',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(121,'PLAYER_IS_HIDDEN_TALENT',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(122,'PLAYER_IS_ALLSTAR',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(123,'PLAYER_YEAR_IN_COLLEGE',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(124,'PLAYER_CONTRACT_VALUE_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(125,'PLAYER_CONTRACT_VALUE_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(126,'PLAYER_FA_NO_CONTRACT_OFFER_IN_30_DAYS',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(127,'PLAYER_WITHIN_7_OF_EVERY_1000TH_HIT_STARTING_AT_2000',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(128,'PLAYER_WITHIN_5_OF_EVERY_100TH_HR_STARTING_AT_300',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(129,'PLAYER_WITHIN_10_OF_EVERY_1000TH_RBI_STARTING_AT_1000',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(130,'PLAYER_WITHIN_5_OF_EVERY_100TH_SB_STARTING_AT_500',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(131,'PLAYER_WITHIN_1_OF_EVERY_100TH_W_STARTING_AT_200',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(132,'PLAYER_WITHIN_15_OF_EVERY_1000TH_K_STARTING_AT_2000',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(133,'PLAYER_WITHIN_1_OF_EVERY_100TH_S_STARTING_AT_400',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(134,'PLAYER_CAREER_BATTING_AVG_MIN',0,1000,'AVG * 1000', 1, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(135,'PLAYER_CAREER_BATTING_AVG_MAX',0,1000,'AVG * 1000', 01, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(136,'PLAYER_CAREER_AB_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(137,'PLAYER_CAREER_AB_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(138,'PLAYER_CAREER_ERA_MIN',0,1000,'ERA * 100', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(139,'PLAYER_CAREER_ERA_MAX',0,1000,'ERA * 100', 0, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(140,'PLAYER_CAREER_IP_MIN',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(141,'PLAYER_CAREER_IP_MAX',0,1,'', 2, '', '', 1, 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(142,'PERSONNEL_IS_FORMER_PLAYER',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(143,'PERSONNEL_IS_RETIRED',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(144,'PERSONNEL_TEAM_WON_LAST_GAME',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(145,'PERSONNEL_CONTRACT_LEFT_MIN',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(146,'PERSONNEL_FA',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(147,'PERSONNEL_IN_FEEDER',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(148,'PERSONNEL_IN_MINORS',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(149,'PERSONNEL_LAST_CONTRACT_YEAR',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(150,'PERSONNEL_YEARS_IN_LEAGUE_MAX',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(151,'PERSONNEL_YEARS_IN_LEAGUE_MIN',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(152,'PERSONNEL_QUALITY_MAX',0,200,'Only Managers', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(153,'PERSONNEL_QUALITY_MIN',0,200,'Only Managers', 1, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(154,'PERSONNEL_ON_MAJOR_LEAGUE_TEAM',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(155,'PERSONNEL_CONTRACT_VALUE_MIN',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(156,'PERSONNEL_CONTRACT_VALUE_MAX',0,1,'', 2, '', '', 1, 6, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(157,'TEAM_NATION_ID',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(158,'TEAM_STATE_ID',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(159,'TEAM_CITY_ID',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(160,'TEAM_FOCUS',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(161,'TEAM_GAMES_OUT_OF_PLAYOFFS_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(162,'TEAM_GAMES_OUT_OF_PLAYOFFS_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(163,'TEAM_DAYS_SINCE_LAST_GAME',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(164,'TEAM_LAST_GAME_AT_HOME',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(165,'TEAM_LAST_GAME_WON',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(166,'TEAM_LAST_GAME_ATTENDANCE_PCT_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(167,'TEAM_LAST_GAME_ATTENDANCE_PCT_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(168,'TEAM_LAST_5_GAMES_WINNING_PCT_MIN',0,1000,'Pct * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(169,'TEAM_LAST_5_GAMES_WINNING_PCT_MAX',0,1000,'Pct * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(170,'TEAM_LAST_10_GAMES_WINNING_PCT_MIN',0,1000,'Pct * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(171,'TEAM_LAST_10_GAMES_WINNING_PCT_MAX',0,1000,'Pct * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(172,'TEAM_LAST_20_GAMES_WINNING_PCT_MIN',0,1000,'Pct * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(173,'TEAM_LAST_20_GAMES_WINNING_PCT_MAX',0,1000,'Pct * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(174,'TEAM_MAGIC_NUMBER_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(175,'TEAM_MAGIC_NUMBER_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(176,'TEAM_MISSED_PLAYOFFS_YEARS_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(177,'TEAM_MISSED_PLAYOFFS_YEARS_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(178,'TEAM_NO_CHAMPIONSHIPS_YEARS_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(179,'TEAM_NO_CHAMPIONSHIPS_YEARS_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(180,'TEAM_BATTING_AVG_MIN',0,1000,'AVG * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(181,'TEAM_BATTING_AVG_MAX',0,1000,'AVG * 1000', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(182,'TEAM_ERA_MIN',0,1000,'ERA * 100', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(183,'TEAM_ERA_MAX',0,1000,'ERA * 100', 1, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(184,'TEAM_RANK_RUNS_SCORED_LAST_SEASON_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(185,'TEAM_RANK_RUNS_SCORED_LAST_SEASON_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(186,'TEAM_RANK_RUNS_ALLOWED_LAST_SEASON_MIN',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(187,'TEAM_RANK_RUNS_ALLOWED_LAST_SEASON_MAX',0,1,'', 2, '', '', 1, 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(188,'NOT_IN_SEASON',0,1,'The league must be in any season BUT the regular season', 2, 'Not in the regular season', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(189,'NOT_IN_SPRING',0,1,'The league must be in any season BUT the spring training', 2, 'Not in the spring', '', 1, 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(190,'NOT_IN_OFFSEASON',0,1,'The league must be in any season BUT the off-season', 2, 'Not In the off-season', '', 1, 4, 1)");

		// Storylines conditions types List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_conditions_types');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(1, 'Value Range')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(2, 'Yes/No')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(3, 'Multi')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(4, 'String')");
		
		// Storylines conditions levels List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_conditions_levels');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_levels VALUES(1,'Storyline')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_levels VALUES(2,'Article')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_levels VALUES(3,'Object')");
		
		// Storylines conditions categories List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_conditions_categories');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(1,'Default')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(2,'Player')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(3,'Team')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(4,'League')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(5,'Event')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(6,'Coach/Manager')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(7,'Owner')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(8,'World')");
		
		// Storylines Articles Results List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`category_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`value_type` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`value_range_min` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`value_range_max` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`options` varchar(500) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`rules` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`active` int(1) NOT NULL DEFAULT '1'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_results');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(1, 'retirement', 'Retire Player','',2,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(2, 'injury', 'Injury','Yes/No',1,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(3, 'injury_description', '','',1,4,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(4, 'injury_length', '','',1,4,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(5, 'injury_cei', '','',1,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(6, 'morale_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(7, 'suspension_games', '','',2,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(8, 'talent_increase', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(9, 'talent_decrease', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(10, 'player_local_popularity_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(11, 'player_national_popularity_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(12, 'player_weight_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(13, 'team_fan_interest_modifier', '','',5,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(14, 'team_fan_loyalty_modifier', '','',5,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(15, 'team_market_size_modifier', '','',5,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(16, 'team_focus_changes', '','',5,3,0,0,'1:Win Now!|2:Neutral|3:Rebuilding','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(17, 'player_placed_on_trading_block', '','',2,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(18, 'player_released', '','',2,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(19, 'player_waives_ntc', '','',2,2,0,0,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(20, 'team_chemistry', '','',5,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(21, 'player_intelligence_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(22, 'player_leader_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(23, 'player_work_ethic_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(24, 'player_greed_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(25, 'player_loyalty_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(26, 'player_play_for_winner_modifier', '','',3,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(27, 'player_pressure_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(28, 'player_speed_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(29, 'player_defense_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(30, 'pitching_stamina_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(31, 'pitching_control_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(32, 'pitching_movement_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(33, 'pitching_velocity_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(34, 'hitting_contact_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(35, 'hitting_power_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(36, 'hitting_eye_modifier', '','',4,1,0,200,'','',1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_results VALUES(37, 'fine_player', '','',2,2,0,0,'','',1)");
		
		// Storylines Articles Results Categories
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_result_categories');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_categories VALUES(1, 'Injuries/DL')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_categories VALUES(2, 'Transactions')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_categories VALUES(3, 'Personality Effects')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_categories VALUES(4, 'Talent Effects')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_categories VALUES(5, 'Team Effects')");
				
		// Storylines Articles Results Value Types
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_result_value_types');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(1, 'Value Range')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(2, 'Yes/No')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(3, 'Multi')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(4, 'String')");
		
		// Text String Tokens
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`category_id` int(11) NOT NULL DEFAULT '1'");
		$this->dbforge->add_field("`active` int(1) NOT NULL DEFAULT '1'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_tokens');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname F L','Steve Garvey', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname L, F','Cruise, Tom', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname L','Garvey', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname F','Steve', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamname','Boston', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamnickname','Red Sox', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'playerposition','center fielder', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#1 F L','Tom Cruise', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#2 F L','Jack Nicholson', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#1 F','Tom', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#2 F','Jack', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#1 L','Cruise', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#2 L','Nicholson', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#1 L, F','Cruise, Tom', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname#2 L, F','Nicholson, Jack', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#1name','Boston', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2name','New York', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#1nickname','Red Sox', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2nickname','Yankees', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'string','', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'string#1','', 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'string#2','', 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'string#3','', 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'string#4','', 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'string#5','', 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personname','Tom Cruise', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'subleaguename','National League', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'subleagueabbr','NL', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'leaguename','Major League Baseball', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'leagueabbr','MLB', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamplayerposition','New York Yankees center fielder', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting avg','.333', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting obp','.455', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting slg','.502', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting rc','73', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting rc27','8.01', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting tavg','.471', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting iso','.277', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ops','1.105', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sbp','78%', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting vorp','65.7', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting pa','15', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting pa word','15 plate appearances', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ab','300', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ab word','300 at-bats', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting h','60', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting h word','60 hits', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting d','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting d word','5 doubles', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting t','13', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting t word','13 triples', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting hr','12', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting hr word','15 home runs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting s','122', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting s word','122 singles', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting tb','254', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting tb word','254 total bases', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sb','41', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sb word','41 stolen bases', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting cs','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting cs word','5 caught stealing', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting rbi','102', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting rbi word','102 runs batted in', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting r','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting r word','5 runs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting bb','75', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting bb word','75 walks', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ibb','21', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ibb word','21 intentional walks', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting k','15', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting k word','5 strikeouts', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting hp','10', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting hp word','15 times hit by pitch', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sh','2', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sh word','2 bunts', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sf','12', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting sf word','12 sacrifice flies', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ci','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ci word','5 catchers interference', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting gdp','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting gdp word','5 times grounded into double play', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting g','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting g word','5 games', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting gs','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting gs word','5 games started', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ebh','16', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting pitches','50', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting pitches word','50 pitches seen', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching era','4.74', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching h9','8.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hr9','0.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bb9','3.8', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching k9','10.1', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching r9','5.6', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching cera','4.13', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching whip','1.32', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching kbb','0.67', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching pig','75', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gbfbp','66.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching avg','.271', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching slg','.777', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching babip','.291', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching obp','.435', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ops','1.019', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching qsp','33.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching winp','66.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching svp','33.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bsvp','22.5%', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching vorp','50.5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ip','171', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ip word','171 innings', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ab','300', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ab word','300 at-bats', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching tb','251', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching tb word','251 total bases allowed', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ha','150', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ha word','150 hits', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching k','221', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching k word','221 strikeouts', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bf','858', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bf word','858 batters faced', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bb','97', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bb word','97 walks', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching r','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching r word','5 runs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching er','71', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching er word','71 earned runs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching g','31', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching g word','31 appearances', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gs','27', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gs word','27 games started', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching w','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching w word','5 wins', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching l','7', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching l word','5 losses', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching s','30', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching s word','30 saves', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sa','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sa word','5 singles allowed', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching da','.629', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching da word','.629 defensive average', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sh','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sh word','5 sacrifice hits allowed', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sf','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sf word','5 sacrifice flies allowed', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ta','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ta word','5 triples allowed', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hra','47', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hra word','47 home runs allowed', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bk','2', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bk word','2 balks', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching iw','13', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching iw word','13 intentional walks', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching wp','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching wp word','5 wild pitches', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hp','12', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hp word','12 hit batters', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gf','52', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gf word','52 games finished', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching dp','17', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching dp word','17 double plays', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching qs','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching qs word','5 quality starts', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching svo','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching svo word','5 save opportunities', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bs','12', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching bs word','12 blown saves', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ra','10', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching ra word','10 relief appearances', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching cg','7', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching cg word','7 complete games', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sho','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sho word','5 shutouts', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sb','5', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching sb word','5 stolen bases', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching cs','7', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching cs word','7 caught stealing', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hld','23', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching hld word','23 holds', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching pi','50', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching pi word','50 pitches', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gb','55', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching gb word','55 ground ball outs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching fb','27', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitching fb word','27 fly ball outs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding g','142', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding g word','142 games', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding gs','137', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding gs word','137 games started', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding tc','454', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding tc word','454 total chances', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding a','20', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding a word','20 assists', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding po','307', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding po word','307 put outs', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding e','25', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding e word','25 errors', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding dp','113', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding dp word','113 double plays', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding pb','7', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding pb word','7 passed balls', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding sba','27', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding sba word','27 stolen bases against', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding rto','11', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding rto word','11 runners thrown out', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding ip','\"1,143\"', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding ip word','\"1,143 defensive innings\"', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding range','4.71', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding pct','.988', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding cera','3.18', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'fielding rtop','27.5%', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batting ebh word','16 extra-base hits', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'playerlink','player_4711.html', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlink','Linktown', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#1link','Boston', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#1link nickname','Boston Red Sox', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2link','New York', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2link nickname','New York Yankees', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlink nick','Boston Links', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'playerposition capital','Center Fielder', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personage','25', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'leaguenation','The United States', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'leagueyear','2006', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink','Steve Link', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink#1','Tom Cruiselink', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink#2','Eddie Moneylink', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamname nick','Boston Red Sox', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamname nickname','Red Sox', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamplayerposition nolink','Boston center fielder', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamplayer nolink','Bostons David Wells', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'playerposition abbr','SS', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'month','June', 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'month previous','May', 8, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlink abbr','BOS', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink f','Steve', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink f l','Steve Link', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink#1 f','Tomlink', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink#1 l','Cruiselink', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink#2 f','Eddielink', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink#2 l','Moneylink', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'personlink l','Link', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlink nickonly','Dodgers', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team record num','5-4', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team record pct','.650', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team division','American League East', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team division position','third', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlevel','Triple-A', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'teamlevel short','AAA', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2 record pct','.605', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2 record num','50-42', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2 division','American League East', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team#2 division position','3rd', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'leaguefinals','World Series', 4, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'team number champions','', 3, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'velocity','', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'player bats','left-handed hitter', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'player bats cap','Left-handed hitter', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'player throws','right-', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'player throws cap','Right-', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'person height','', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'person weight','', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'player best pitch','', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'batter position','', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'player repertoire','', 2, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens VALUES(0, 'pitcher role','starter', 2, 1)");

		// Storylines Tokens categories List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_tokens_categories');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(1,'Default')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(2,'Player')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(3,'Team')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(4,'League')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(5,'Event')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(6,'Coach')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(7,'Owner')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_tokens_categories VALUES(8,'World')");
		
	}
	
	//--------------------------------------------------------------------
	
	public function down() 
	{
		$prefix = $this->db->dbprefix;
		
		$query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = 'Storylines.Settings.Manage'");
		foreach ($query->result_array() as $row)
		{
			$permission_id = $row['permission_id'];
			$this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
		}
		//delete the permission
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Storylines.Settings.Manage')");

		$query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = 'Storylines.Content.Manage'");
		foreach ($query->result_array() as $row)
		{
			$permission_id = $row['permission_id'];
			$this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
		}
		//delete the permission
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Storylines.Content.Manage')");

		$query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = 'Storylines.Content.Add'");
		foreach ($query->result_array() as $row)
		{
			$permission_id = $row['permission_id'];
			$this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
		}
		//delete the permission
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Storylines.Content.Add')");
		
		$query = $this->db->query("SELECT permission_id FROM {$prefix}permissions WHERE name = 'Storylines.Data.Manage'");
		foreach ($query->result_array() as $row)
		{
			$permission_id = $row['permission_id'];
			$this->db->query("DELETE FROM {$prefix}role_permissions WHERE permission_id='$permission_id';");
		}
		//delete the permission
		$this->db->query("DELETE FROM {$prefix}permissions WHERE (name = 'Storylines.Data.Manage')");

		// drop tables
		$this->dbforge->drop_table('storylines');
		$this->dbforge->drop_table('storylines_articles');
		$this->dbforge->drop_table('storylines_triggers');
		$this->dbforge->drop_table('storylines_data_objects');
		$this->dbforge->drop_table('storylines_conditions');
		$this->dbforge->drop_table('storylines_history');
		$this->dbforge->drop_table('storylines_article_results');
		$this->dbforge->drop_table('storylines_article_predecessors');
		$this->dbforge->drop_table('list_storylines_random_frequencies');
		$this->dbforge->drop_table('list_storylines_articles_message_types');
		$this->dbforge->drop_table('list_storylines_triggers');
		$this->dbforge->drop_table('list_storylines_data_objects');
		$this->dbforge->drop_table('list_storylines_conditions');
		$this->dbforge->drop_table('list_storylines_conditions_categories');
		$this->dbforge->drop_table('list_storylines_conditions_types');
		$this->dbforge->drop_table('list_storylines_conditions_levels');
		$this->dbforge->drop_table('list_storylines_results');
		$this->dbforge->drop_table('list_storylines_results');
		$this->dbforge->drop_table('list_storylines_result_categories');
		$this->dbforge->drop_table('list_storylines_result_value_types');
		$this->dbforge->drop_table('list_storylines_categories');
		$this->dbforge->drop_table('list_storylines_author_status');
		$this->dbforge->drop_table('list_storylines_publish_status');
		$this->dbforge->drop_table('list_storylines_tokens');
		$this->dbforge->drop_table('list_storylines_tokens_categories');
	}
	
	//--------------------------------------------------------------------
}