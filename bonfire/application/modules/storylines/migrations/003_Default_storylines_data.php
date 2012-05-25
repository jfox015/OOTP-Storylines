<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Default_storylines_data extends Migration {

    public function up()
	{
		$prefix = $this->db->dbprefix;

        // STORYLINE 1
        $comments = $this->db->table_exists('comments_threads');
        $comments_thread_id = 0;
        if ($comments) {
            $this->db->query("INSERT INTO {$prefix}comments_threads VALUES(0,".time().",0,'storylines')");
            $comments_thread_id = $this->db->insert_id();
            $this->db->query("INSERT INTO {$prefix}comments VALUES(0, {$comments_thread_id},'This is a default comment. Do with it as you will.',".time().",1,".time().",'', 0,1)");
        }
        $this->db->query("INSERT INTO {$prefix}storylines VALUES(0, 'Test Story','<b>This is a test</b><br />Testing how this all works out.</b>','news,article,first',1,1,1,{$comments_thread_id},5000,".time().",1,".time().",1,0,0,'')");
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

    }
	//--------------------------------------------------------------------
	
	public function down() 
	{
        $prefix = $this->db->dbprefix;


    }
	
	//--------------------------------------------------------------------
	
}