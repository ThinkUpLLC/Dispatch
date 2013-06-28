<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';
require_once dirname(__FILE__) . '/ModelTest.php';
require_once dirname(__FILE__) . '/CrawlFixtureDataSet.php';

class CrawlStatsDAOTest extends ModelTest
{


    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }


    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage crawl_start must be a valid unix timestamp
     */
    public function testSaveStatsCrawlStartArgs() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => 'bla',
            'crawl_time' => 1,
            'crawl_start' => 1,
            'crawl_finish' => 1,
        ));
    }

    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage crawl_finish must be a valid unix timestamp
     */
    public function testSaveStatsBadCrawlEndArgs() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => 'bla',
            'crawl_time' => 1,
            'crawl_start' => 1371432760,
            'crawl_finish' => 1,
        ));
    }

    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage crawl_finish must be greater or equal to crawl_start
     */
    public function testSaveStatsCrawlEndMustBeGreaterOrEqualThanStartArgs() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => 'bla',
            'crawl_time' => 1,
            'crawl_start' => 1371432760,
            'crawl_finish' => 1371432759,
        ));
    }

    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage crawl_time must be a valid seconds integer
     */
    public function testSaveStatsCrawlTimeValidInt() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => 'bla',
            'crawl_time' => 'a',
            'crawl_start' => 1371432760,
            'crawl_finish' => 1371432765,
        ));
    }

    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage requires an install_name
     */
    public function testSaveStatsValidInstallName() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => '',
            'crawl_time' => '5',
            'crawl_start' => 1371432760,
            'crawl_finish' => 1371432765,
        ));
    }

    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage requires a log
     */
    public function testSaveStatsValidLog() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => 'test install',
            'crawl_time' => '5',
            'crawl_start' => 1371432760,
            'crawl_finish' => 1371432765,
        ));
    }

    /**
     * @expectedException        \thinkup\exceptions\InvalidArgumentException
     * @expectedExceptionMessage requires a valid status
     */
    public function testSaveStatsValidStatus() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats_dao->saveStatus(array(
            'install_name' => 'test install',
            'crawl_time' => '5',
            'crawl_start' => 1371432760,
            'crawl_finish' => 1371432765,
            'log' => 'a valid log',
            'crawl_status' => 'a'
        ));
    }

    /**
     *
     */
    public function testSaveStats() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $insertid = $stats_dao->saveStatus(array(
            'install_name' => 'test install',
            'crawl_time' => '5',
            'crawl_start' => 1371432760,
            'crawl_finish' => 1371432765,
            'log' => 'this is a big fat log message, and stuff',
            'crawl_status' => 0
        ));
        $this->assertRegExp('/^\d+$/', $insertid);
        $this->assertGreaterThan(0, $insertid);

        $stmt = \thinkup\model\CrawlStatsDAO::$PDO->query( "select install_name, " .
        "crawl_time, unix_timestamp(crawl_start) as crawl_start, " .
        "unix_timestamp(crawl_finish) as crawl_finish, crawl_status " .
        "from crawl_status where id = $insertid" );
        $data = $stmt->fetch();
        //var_dump($data);
        $this->assertEquals('test install', $data['install_name']);
        $this->assertEquals(5, $data['crawl_time']);
        $this->assertEquals(1371432760, $data['crawl_start']);
        $this->assertEquals(1371432765, $data['crawl_finish']);
        $this->assertEquals(0, $data['crawl_status']);

        $stmt = \thinkup\model\CrawlStatsDAO::$PDO->query( "select * from crawl_log where crawl_status_id = $insertid");
        $data = $stmt->fetch();
        $this->assertEquals($insertid, $data['crawl_status_id']);
        $this->assertEquals('this is a big fat log message, and stuff', $data['crawl_log']);
        //sleep(1000);
    }

    /**
     * test fetching crawl stats
     */
    public function testGetCrawlStats() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $stats = $stats_dao->getCrawlStats();
        
        $this->assertEquals(2, sizeof($stats));

        $this->assertEquals(0, $stats[0]['crawl_status']);
        $this->assertEquals(100, $stats[0]['average']);
        $this->assertEquals(100, $stats[0]['max']);
        $this->assertEquals(100, $stats[0]['min']);
        $this->assertEquals(1, $stats[0]['count']);

        $this->assertEquals(1, $stats[1]['crawl_status']);
        $this->assertEquals(38, $stats[1]['average']);
        $this->assertEquals(140, $stats[1]['max']);
        $this->assertEquals(10, $stats[1]['min']);
        $this->assertEquals(5, $stats[1]['count']);

        // filter by install 'test 1'
        $stats = $stats_dao->getCrawlStats('test 1');

        $this->assertEquals(2, sizeof($stats));

        $this->assertEquals(0, $stats[0]['crawl_status']);
        $this->assertEquals(100, $stats[0]['average']);
        $this->assertEquals(100, $stats[0]['max']);
        $this->assertEquals(100, $stats[0]['min']);
        $this->assertEquals(1, $stats[0]['count']);

        $this->assertEquals(1, $stats[1]['crawl_status']);
        $this->assertEquals(57, $stats[1]['average']);
        $this->assertEquals(140, $stats[1]['max']);
        $this->assertEquals(10, $stats[1]['min']);
        $this->assertEquals(3, $stats[1]['count']);

        // filter by install 'test 2'
        $stats = $stats_dao->getCrawlStats('test 2');
        $this->assertEquals(1, sizeof($stats));
        $this->assertEquals(1, $stats[0]['crawl_status']);
        $this->assertEquals(10, $stats[0]['average']);
        $this->assertEquals(10, $stats[0]['max']);
        $this->assertEquals(10, $stats[0]['min']);
        $this->assertEquals(2, $stats[0]['count']);

    }

    /**
     * test fetching crawl data
     */
    public function testGetCrawlData() {
        $stats_dao = new \thinkup\model\CrawlStatsDAO();
        $data = $stats_dao->getCrawlData();
        $this->assertEquals(6, sizeof($data));
        $this->assertEquals('test 1', $data[0]['install_name']);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals(1, $data[0]['crawl_status']);
        $this->assertEquals(140, $data[0]['crawl_time']);
        $this->assertEquals('test 2', $data[2]['install_name']);
        $this->assertEquals(3, $data[2]['id']);

        // for test 1 install
        $data = $stats_dao->getCrawlData('test 1');
        $this->assertEquals(4, sizeof($data));
        $this->assertEquals('test 1', $data[0]['install_name']);
        $this->assertEquals(1, $data[0]['id']);
        $this->assertEquals(1, $data[0]['crawl_status']);
        $this->assertEquals(140, $data[0]['crawl_time']);
        $this->assertEquals('test 1', $data[2]['install_name']);
        $this->assertEquals(4, $data[2]['id']);

        // for test 2 install
        $data = $stats_dao->getCrawlData('test 2');
        $this->assertEquals(2, sizeof($data));
        $this->assertEquals('test 2', $data[0]['install_name']);
        $this->assertEquals(3, $data[0]['id']);
        $this->assertEquals(1, $data[0]['crawl_status']);
        $this->assertEquals(10, $data[0]['crawl_time']);
        $this->assertEquals('test 2', $data[1]['install_name']);
        $this->assertEquals(5, $data[1]['id']);
    }

}


