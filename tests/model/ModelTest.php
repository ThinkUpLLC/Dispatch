<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';

class ModelTest extends PHPUnit_Framework_TestCase
{

    static $tables = array('crawl_status', 'crawl_log');

    public static function setUpBeforeClass()
    {
        $cmo = new \thinkup\DispatchParent();
        $config = new \thinkup\config\Config();
        $test_dbname = $cmo->config('db_name') . '_test';
        $dbname = $cmo->config('db_name');        
        $crawl_stats_dao = new \thinkup\model\CrawlStatsDAO();
        $crawl_stats_dao->connect();
        $crawl_stats_dao->execute("drop database if exists $test_dbname");
        $crawl_stats_dao->execute("create database $test_dbname");
        $tables = self::$tables;
        foreach($tables as $table) {
            $crawl_stats_dao->execute("CREATE TABLE $test_dbname.$table LIKE $dbname.$table");
        }
        $config->set('db_name', $test_dbname);
        $crawl_stats_dao->disconnect();
    }

    public function setUp()
    {
        $this->dao = new \thinkup\model\PDODAO();
        $this->dao->connect();
        $this->truncateTables();
        parent::setUp();
    }

    public function tearDown()
    {
        $this->truncateTables();
        $this->dao->disconnect();
        parent::setUp();
    }

    protected function truncateTables() {
        $config = new \thinkup\config\Config();
        $tables = self::$tables;
        foreach($tables as $table) {
            $this->dao->execute("truncate table $table");
        }
    }
}