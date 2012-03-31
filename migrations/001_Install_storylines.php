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

		// ADD TABLES
		
		// Storylines
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field('`title` varchar(255) NOT NULL');
		$this->dbforge->add_field('`description` LONGTEXT NOT NULL');
		$this->dbforge->add_field("`tags` varchar(255) NOT NULL");
		
		$this->dbforge->add_field("`review_status_id` tinyint(1) NOT NULL DEFAULT '0'");
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
		$this->dbforge->add_field("`object_type` int(1) NOT NULL DEFAULT '0'");
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
		$this->dbforge->add_field("`results_id` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`result_type` int(1) NOT NULL DEFAULT '0'");
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
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0)");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines VALUES(0, 'Test Story','<b>This is a test</b><br />Testing how this all works out.</b>','news,article,first',1,1,1,{$comments_thread_id},5000,".time().",1,".time().",1,0)");
        $storyline_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$storyline_id}, 1,'Added default storyline and article','','',".time().",1)");


		// ARTICLE 1
		if ($comments) {
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0)");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines_articles VALUES(0, {$storyline_id}, 'Test Article','<b>This is a test</b><br />Testing how this all works out.</b>',0,0,1,'',{$comments_thread_id},".time().",2,".time().",1,0)");
		$prev_article_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$prev_article_id}, 2,'Added article to storlyine','','',".time().",1)");
		
		// ARTICLE 2
		if ($comments) {
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0)");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines_articles VALUES(0, {$storyline_id}, 'A Child test article','<b>This is a test</b><br />Testing how this all works out.</b>',7,14,1,'',{$comments_thread_id},".time().",3,".time().",1,0)");
		$article_id = $this->db->insert_id();
		$this->db->query("INSERT INTO {$prefix}storylines_history VALUES(0, {$article_id}, 2,'Added article to storlyine','','',".time().",1)");
		$this->db->query("INSERT INTO {$prefix}storylines_article_predecessors VALUES(0, {$storyline_id}, {$article_id}, {$prev_article_id})");
		$prev_article_id = $article_id;
		
		// ARTICLE 3
		if ($comments) {
			$this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0)");
			$comments_thread_id = $this->db->insert_id();
			$this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0)");
		}
		$this->db->query("INSERT INTO {$prefix}storylines_articles VALUES(0, {$storyline_id}, 'A second level child test Article','<b>This is a test</b><br />Testing how this all works out.</b>',7,21,1,'',{$comments_thread_id},".time().",1,".time().",1,0)");
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
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Rarelyn',2000)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Standard',5000)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Often',6500)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_random_frequencies VALUES(0,'Very Often',8000)");
		
		// Storylines Article message types List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_articles_message_types');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_articles_message_types VALUES(0,'No Message')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_articles_message_types VALUES(0,'League News')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_articles_message_types VALUES(0,'Personal Message')");
		
		// Storylines Triggers List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_triggers');
		
		// Storylines Data Objects List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_data_objects');

		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'BENCH_COACH', 'Bench Coach','Team Bench Coach')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'DOCTOR', 'Doctor','Team Trainer/Doctor')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'HEAD_SCOUT', 'head Scout','Teams Head Scout')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'HIGHER_LEAGUE', 'Higher League','Available for Minors/feeders only')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'LEAGUE', 'League','Not necessary. Always added by default.')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'LOWER_LEAGUE', 'Minor League (Any)','Team Bench Coach')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'MANAGER', 'Manager','Team Bench Coach')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'NON_TEAM_MATE', 'Non teammate','Team Bench Coach')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'OTHER_LEAGUE', 'Other League','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'OTHER_TEAM', 'Other Team','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'OWNER', 'Team owner','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'PITCHING_COACH', 'pitching Coach','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'PLAYER', 'Player','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'TEAM', 'Team','')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_data_objects VALUES(0,'TEAM_MATE', 'teammate','')");

		// Storylines categories List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_categories');

		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'default', 'Default')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'charity', 'Charity')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'coach', 'Coach Related')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'controversey', 'Controversey')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'death', 'Death')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'family', 'Family')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'funny', 'Funny')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'injury', 'Injuries')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'manager', 'Manager Related')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_categories VALUES(0,'owner', 'Owner Related')");

		// Storylines review status List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_review_status');

		$this->db->query("INSERT INTO {$prefix}list_storylines_review_status VALUES(0, 'unknown', 'Unknown')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_review_status VALUES(0, 'open', 'Open for Conttibutions')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_review_status VALUES(0, 'locked', 'Locked')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_review_status VALUES(0, 'add', 'Add Articles')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_review_status VALUES(0, 'correct', 'Corrections Only')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_review_status VALUES(0, 'change', 'Changes Needed. See Comments.')");

		// Storylines publish status List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_publish_status');

		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(0, 'unknown', 'Unknown')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(0, 'added', 'Added')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(0, 'review', 'In Review')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(0, 'approved', 'Approved for Publish')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(0, 'rejected', 'Rejected')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_publish_status VALUES(0, 'archived', 'Archived')");

		// Storylines Conditions List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`name` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`description` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`type` int(1) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`options` varchar(1000) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`value_range_min` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`value_range_max` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`level_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`category_id` int(11) NOT NULL DEFAULT '0'");
		$this->dbforge->add_field("`active` int(1) NOT NULL DEFAULT '1'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_conditions');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_AGE_MAX', '', '', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_AGE_MIN', '', '', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_BMI_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_BMI_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_FOREIGN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_GREED_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_GREED_MIN', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_HOMETOWN_DISTANCE_FROM_TEAM_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_HOMETOWN_DISTANCE_FROM_TEAM_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_LOYALTY_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_LOYALTY_MIN', '', 'Player X must have Loyalty rating is at least/below X (on the 1-200 scale)', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_NATION_ID', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PERSON_STATE_ID', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_AB_MAX', '', '', 0, '', 0, 999, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_AB_MIN', '', '', 0, '', 0, 999, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_ATTENDED_COLLEGE', '', '', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_BMI_MAX', 'Body Mass Index Max', '', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_BMI_MIN', 'Body Mass Index Min', 'Player X is at least 200 lbs. (if below 183 cm in height), or at least 220 lbs. (if between 183-198 cm in height)', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_AB_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_AB_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_BATTING_AVG_MAX', '', 'AVG * 1000', 0, '', 0, 1000, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_BATTING_AVG_MIN', '', 'AVG * 1000', 0, '', 0, 1000, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_ERA_MAX', '', 'ERA * 100', 0, '', 0, 1000, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_ERA_MIN', '', 'ERA * 100', 0, '', 0, 1000, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_IP_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CAREER_IP_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CONTRACT_LEFT_MIN', '', 'Player X must have at least one additional non-optional year remaining on his contract	', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CONTRACT_VALUE_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_CONTRACT_VALUE_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_DESIRE_FOR_WINNER_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_DESIRE_FOR_WINNER_MIN', '', 'Player Xs Desire for Winner rating is at least/below X (on 1-200 scale)', 1, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_DRAFT_ELIGIBLE', '', '', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_FA', 'Free Agent', 'Player X is a free agent', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_FA_ELIGIBLE', 'Free Agent Elidgible', 'Player X qualifies for free agency', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_FA_NO_CONTRACT_OFFER_IN_30_DAYS', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_FOREIGN', '', 'Player X has a nationality that is different/same as than the parent leagues nation', 0, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_HAS_CEI', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_HAS_NTC', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_INJURY_PRONENESS_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_INJURY_PRONENESS_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_INTELLIGENCE_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_INTELLIGENCE_MIN', '', 'Player Xs Intelligence rating is at least/below X (on 1-200 scale)', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IN_FEEDER', '', '', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IN_MINORS', '', 'Player X is on a major or minor league roster within Team Ys organization', 0, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IP_MAX', '', '', 0, '', 0, 999, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IP_MIN', '', 'Player X accumulated at least 50 IP in the previous season', 0, '', 0, 999, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_ALLSTAR', '', '', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_DFA', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_HALL_OF_FAMER', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_HIDDEN_TALENT', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_INJURED', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_LEFTHANDED', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_ON_MAJOR_LEAGUE_TEAM', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_ON_TRADE_BLOCK', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_ON_UNTOUCHABLE_LIST', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_ON_WAIVERS', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_RETIRED', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_IS_UPCOMING_FA', '', 'Player X is a pending free agent and hasnt signed an extension', 0, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_LAST_CONTRACT_YEAR', '', '', 1, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_LEADERSHIP_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_LEADERSHIP_MIN', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_LOCAL_POPULARITY_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_LOCAL_POPULARITY_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_MLB_SERVICE_MAX', '', '', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_MLB_SERVICE_MIN', '', 'Player X has at least/below X years of major league service time', 0, '', 0, 99, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_MORALE_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_MORALE_MIN', '', 'player on active roster has a morale of Angry', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_NATIONALITY_ID', '', '', 0, '', 0, 9999, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_NATIONAL_POPULARITY_MAX', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_NATIONAL_POPULARITY_MIN', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_ON_ACTIVE', '', 'Player X and Player Y are both on the active roster', 0, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_ON_SECONDARY', '', 'Player X must be on Team Ys secondary roster', 0, '', 0, 1, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PERSONALITY_IS_CANCER', 'Personality: Cancer', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PERSONALITY_IS_CAPTAIN', 'Personality: Captain', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PERSONALITY_IS_FAN_FAVORITE', 'Personality: Fan Favorite', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PERSONALITY_IS_HUMBLE', 'Personality: Humble', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PERSONALITY_IS_SELFISH', 'Personality: Selfish', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_POSITION', 'Position', 'pitcher=1, catcher=2, first_base=3, second_base=4=, third_base=5, shortstop=6, left_field=7, center_field=8, right_field=9, designated_hitter=10, starter=11, reliever=12, closer=13, all_batters=14, highschool_players=15, college_players=16', 3, 'Pitcher:1|Catcher:2|First Base:3|Second Base:4|Third Base:5|Dhortstop:6|Left_field:7|center_field:8|right_field:9|designated_hitter:10|starter:11|reliever:12|closer:13|all Batters:14|highschool_players:15|college_players:16', 1, 16, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PRO_SERVICE_MAX', 'Professional service time Max', '', 0, '', 0, 99, 1, 1,1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_PRO_SERVICE_MIN', 'Professional service time Min', 'Player has accrued no professional service time', 0, '', 0, 99,1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_QUALITY_MAX', '', 'Sum of players Contact, Gap, Power and Eye/Patience actual ratings (for hitters) or players Fastball, Stuff, Movement and Control actual ratings (for pitchers) is at least/below X', 0, '', 0, 7, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_QUALITY_MIN', '', 'sum of players Contact, Gap, Power and Eye/Patience actual ratings (for hitters) or players Fastball, Stuff, Movement and Control actual ratings (for pitchers) is at least/below X', 0, '', 0, 7, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_SEASON_PERFORMANCE_MAX', '', '', 0, '', -25, 25, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_SEASON_PERFORMANCE_MIN', '', 'Players VORP is not in the top 5 of players on active roste', 0, '', -25, 25, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_10_OF_EVERY_1000TH_RBI_STARTING_AT_1000', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_15_OF_EVERY_1000TH_K_STARTING_AT_2000', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_1_OF_EVERY_100TH_S_STARTING_AT_400', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_1_OF_EVERY_100TH_W_STARTING_AT_200', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_5_OF_EVERY_100TH_HR_STARTING_AT_300', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_5_OF_EVERY_100TH_SB_STARTING_AT_500', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WITHIN_7_OF_EVERY_1000TH_HIT_STARTING_AT_2000', '', '', 0, '', 0, 0, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WORK_ETHIC_MAX', '', '', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_WORK_ETHIC_MIN', '', 'Player Xs Work Ethic rating is at least/below X (on 1-200 scale)', 0, '', 0, 200, 1, 1, 1)");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions VALUES(0,'PLAYER_YEAR_IN_COLLEGE', '', '', 0, '', 0, 0, 1, 1, 1)");
		
		// Storylines conditions types List
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_conditions_types');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(1, 'Value Range')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(2, 'Yes/No')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_types VALUES(3, 'Multi')");
		
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
		$this->db->query("INSERT INTO {$prefix}list_storylines_conditions_categories VALUES(6,'Coach')");
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
		
		// Storylines Articles Results Value Types
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
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_result_value_types');
		
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(1, 'Value Range')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(2, 'Yes/No')");
		$this->db->query("INSERT INTO {$prefix}list_storylines_result_value_types VALUES(3, 'Multi')");
		
		// Storylines Articles Results Value Types
		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
		$this->dbforge->add_field("`slug` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_field("`value` varchar(255) NOT NULL DEFAULT ''");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('list_storylines_tokens');
		
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
		$this->dbforge->drop_table('list_storylines_result_categories');
		$this->dbforge->drop_table('list_storylines_result_value_types');
		$this->dbforge->drop_table('list_storylines_categories');
		$this->dbforge->drop_table('list_storylines_review_status');
		$this->dbforge->drop_table('list_storylines_publish_status');
		$this->dbforge->drop_table('list_storylines_tokens');
	}
	
	//--------------------------------------------------------------------
}