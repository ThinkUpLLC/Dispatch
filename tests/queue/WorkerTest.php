<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';
require_once dirname(__FILE__) . '/../model/ModelTest.php';

class WorkerTest extends ModelTest
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
     * @expectedException         thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Invalid workload json
     */
    public function testRunJobMalformedJson() {
        $client = new \thinkup\queue\Worker();
        $client->processJob(new MockJob('a_handle', 'bad json'));
    }

    /**
     * @expectedException         thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl Jobs need a 'installation_name', timezone', 'db_name', 'db_host' and 'db_port' or a 'db_socket'
     */
    public function testRunJobBadJson() {
        $client = new \thinkup\queue\Worker();
        $client->processJob(new MockJob('a_handle', '{"bad":"json"}'));
    }

    public function testRunJobFail() {
        $worker = new \thinkup\queue\Worker();
        \thinkup\queue\Worker::$chameleon_cmd = "cat ";
        $jobjson = '{"installation_name":"mwilkie","timezone":"America/Los_Angeles","db_host":"localhost","db_name":"thinkup_20120911","db_socket":"/tmp/mysql.sock","db_port":""}';
        $worker->processJob(new MockJob('a_handle', $jobjson));

        $stmt = \thinkup\model\CrawlStatsDAO::$PDO->query( "select id, install_name, " .
        "crawl_time, unix_timestamp(crawl_start) as crawl_start, " .
        "unix_timestamp(crawl_finish) as crawl_finish, crawl_status " .
        "from crawl_status");
        $data = $stmt->fetch();
        $this->assertEquals($data["install_name"], 'test 1');
        $this->assertGreaterThanOrEqual(0, $data["crawl_time"]);
        $almost_now = time() - 400;
        $this->assertGreaterThan($almost_now, $data["crawl_start"]);
        $this->assertGreaterThan($almost_now, $data["crawl_finish"]);
        $this->assertGreaterThanOrEqual($data["crawl_time"], $data["crawl_finish"]);
        $this->assertGreaterThan(0, $data["crawl_status"]);
        $stmt = \thinkup\model\CrawlStatsDAO::$PDO->query( "select * from crawl_log where crawl_status_id = " . $data['id']);
        $data = $stmt->fetch();
        $this->markTestSkipped("need to flesh out crawl log fixture");
        
        $this->assertRegExp('/cat.*Failed/', $data['crawl_log']);
    }

    public function testRunJob() {
        $worker = new \thinkup\queue\Worker();
        \thinkup\queue\Worker::$chameleon_cmd = "echo 'happy test'; echo";
        $jobjson = '{"installation_name":"mwilkie","timezone":"America/Los_Angeles","db_host":"localhost","db_name":"thinkup_20120911","db_socket":"/tmp/mysql.sock","db_port":""}';
        $worker->processJob(new MockJob('a_handle', $jobjson));

        $stmt = \thinkup\model\CrawlStatsDAO::$PDO->query( "select id, install_name, " .
        "crawl_time, unix_timestamp(crawl_start) as crawl_start, " .
        "unix_timestamp(crawl_finish) as crawl_finish, crawl_status " .
        "from crawl_status");
        $data = $stmt->fetch();
        $this->assertEquals($data["install_name"], 'test 1');
        $this->assertGreaterThanOrEqual(0, $data["crawl_time"]);
        $almost_now = time() - 400;
        $this->assertGreaterThan($almost_now, $data["crawl_start"]);
        $this->assertGreaterThan($almost_now, $data["crawl_finish"]);
        $this->assertGreaterThanOrEqual($data["crawl_time"], $data["crawl_finish"]);
        $this->assertEquals(1, $data["crawl_status"]);
        $stmt = \thinkup\model\CrawlStatsDAO::$PDO->query( "select * from crawl_log where crawl_status_id = " . $data['id']);
        $data = $stmt->fetch();
        
        $this->markTestSkipped("need to flesh out crawl log fixture");
        
        $this->assertRegExp('/happy test/', $data['crawl_log']);
    }
}

class MockJob
{

    var $handle = '';
    var $workload = '';
    public function __construct($handle, $workload) {
        $this->handle = $handle;
        $this->workload = $workload;
    }

    public function handle() {
        return $this->handle;
    }

    public function workload() {
        return $this->workload;
    }
}