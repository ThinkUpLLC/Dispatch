<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';
require_once dirname(__FILE__) . '/CrawlFixtureDataSet.php';

class ModelTest extends PHPUnit_Extensions_Database_TestCase
{

    static $tables = array('crawl_status', 'crawl_log');

    public function getConnection() {
        $this->dao = new \thinkup\model\PDODAO();
        $this->dao->connect();
        return $this->createDefaultDBConnection( \thinkup\model\PDODAO::$PDO );        
    }

    /**
     * our fixture builder
     */
    public function getDataSet() {

        $date_10_ago = date( 'Y-m-d H:i:s', time() - 10 );
        $date_20_ago = date( 'Y-m-d H:i:s', time() - 20 ); 
        $date_30_ago = date( 'Y-m-d H:i:s', time() - 30 ); 
        $date_40_ago = date( 'Y-m-d H:i:s', time() - 40 ); 
        $date_50_ago = date( 'Y-m-d H:i:s', time() - 60 ); 
        $date_60_ago = date( 'Y-m-d H:i:s', time() - 60 ); 
        $date_160_ago = date( 'Y-m-d H:i:s', time() - 160 );
        $date_300_ago = date( 'Y-m-d H:i:s', time() - 300 );

        return new CrawlFixtureDataSet(array(
            // our crawl status inserts
            'crawl_status' => array(
                // 140 second successfull run
                array('id' => 1, 'install_name' => 'test 1', 'crawl_time' => 140, 'crawl_start' => $date_300_ago, 'crawl_finish' => $date_160_ago, 'crawl_status' => 1),
                // 100 second bad run
                array('id' => 2, 'install_name' => 'test 1', 'crawl_time' => 100, 'crawl_start' => $date_160_ago, 'crawl_finish' => $date_60_ago, 'crawl_status' => 0),
                // 10 second good run second install
                array('id' => 3, 'install_name' => 'test 2', 'crawl_time' => 10, 'crawl_start' => $date_50_ago, 'crawl_finish' => $date_60_ago, 'crawl_status' => 1),
                // 10 second good run first install
                array('id' => 4, 'install_name' => 'test 1', 'crawl_time' => 10, 'crawl_start' => $date_50_ago, 'crawl_finish' => $date_60_ago, 'crawl_status' => 1),
                // 10 second good run second install
                array('id' => 5, 'install_name' => 'test 2', 'crawl_time' => 10, 'crawl_start' => $date_40_ago, 'crawl_finish' => $date_40_ago, 'crawl_status' => 1),
                // 20 second good run first install
                array('id' => 6, 'install_name' => 'test 1', 'crawl_time' => 20, 'crawl_start' => $date_10_ago, 'crawl_finish' => $date_30_ago, 'crawl_status' => 1),
            )
        ));
    }

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
        parent::tearDown();
    }

    protected function truncateTables() {
        $config = new \thinkup\config\Config();
        $tables = self::$tables;
        foreach($tables as $table) {
            //echo "truncate table $table\n";
            $this->dao->execute("truncate table $table");
        }
    }
}