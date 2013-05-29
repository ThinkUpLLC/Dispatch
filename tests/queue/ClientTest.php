<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';

class ClientTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /* include DispatchParent */
        require_once substr(dirname(__FILE__), 0, -11) . '/lib/DispatchParent.php';
        \thinkup\DispatchParent::init();

        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testAddJobsNoArgs() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs();
    }

    /**
     * @expectedException         \thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl jobs is not a valid list
     */
    public function testAddJobsNoAnArray() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs('no an array');
    }

    /**
     * @expectedException         \thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl jobs is not a valid list
     */
    public function testAddJobsEmptyArray() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs(array());
    }


    /**
     * @expectedException         \thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl jobs is not a valid list
     */
    public function testAddJobsBadArray() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs(array('bla' => 'ma'));
    }

    /**
     * @expectedException         \thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl Jobs need a 'installation_name', timezone', 'db_name', 'db_host' and 'db_port' or a 'db_socket'
     */
    public function testAddJobsMissingHost() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs(array(array('database_name' => 'test', 'port' => 3306)));
    }

    /**
     * @expectedException         \thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl Jobs need a 'installation_name', timezone', 'db_name', 'db_host' and 'db_port' or a 'db_socket'
     */
    public function testAddJobsMissingPort() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs(array(array('database_name' => 'test', 'database_host' => '127.0.0.1')));
    }

    /**
     * @expectedException         \thinkup\exceptions\InvalidCrawlJobList
     * @expectedExceptionMessage  Crawl Jobs need a 'installation_name', timezone', 'db_name', 'db_host' and 'db_port' or a 'db_socket'
     */
    public function testAddJobsMissingTimezone() {
        $client = new \thinkup\queue\Client();
        $client->queueCrawlJobs(array(array("installation_name" => "mwilkie", 'database_name' => 'test', 'database_host' => '127.0.0.1')));
    }

    /**
     *
     */
    public function testAddASingleJob() {

        $job = array("installation_name" => "mwilkie","timezone" => "America/Los_Angeles",
        "db_host" => "localhost","db_name" => "thinkup_20120911","db_socket" => "/tmp/mysql.sock","db_port" => "");

        // mock the header method in our root controller
        $queue = $this->getMock('\GearmanClient', array('doBackground'));
        $queue->expects($this->once())
                 ->method('doBackground')
                 ->with( $this->equalTo('crawl'), $this->equalTo( json_encode($job) ) );

        $client = new \thinkup\queue\Client($queue);
        $cnt = $client->queueCrawlJobs(array($job));
        $this->assertEquals($cnt, 1);
    }

    /**
     *
     */
    public function testOnlyDBHost() {

        $job = array("installation_name" => "mwilkie","timezone" => "America/Los_Angeles",
        "db_host" => "localhost","db_name" => "thinkup_20120911","db_port" => "");

        // mock the header method in our root controller
        $queue = $this->getMock('\GearmanClient', array('doBackground'));
        $queue->expects($this->once())
                 ->method('doBackground')
                 ->with( $this->equalTo('crawl'), $this->equalTo( json_encode($job) ) );

        $client = new \thinkup\queue\Client($queue);
        $cnt = $client->queueCrawlJobs(array($job));
        $this->assertEquals($cnt, 1);

    }

    /**
     *
     */
    public function testOnlyDBSocket() {

        $job = array("installation_name" => "mwilkie","timezone" => "America/Los_Angeles",
        "db_host" => "localhost","db_name" => "thinkup_20120911","db_socket" => "/tmp/mysql.sock");

        // mock the header method in our root controller
        $queue = $this->getMock('\GearmanClient', array('doBackground'));
        $queue->expects($this->once())
                 ->method('doBackground')
                 ->with( $this->equalTo('crawl'), $this->equalTo( json_encode($job) ) );

        $client = new \thinkup\queue\Client($queue);
        $cnt = $client->queueCrawlJobs(array($job));
        $this->assertEquals($cnt, 1);

    }

    /**
     *
     */
    public function testAddMultipleJobs() {
    
        $job1 = array("installation_name" => "mwilkie2","timezone" => "America/Los_Angeles",
        "db_host" => "localhost","db_name" => "thinkup_20120911","db_socket" => "/tmp/mysql.sock");
    
        $job2 = array("installation_name" => "mwilkie2","timezone" => "America/Los_Angeles",
        "db_host" => "localhost","db_name" => "thinkup_20120911","db_socket" => "/tmp/mysql.sock");
        
        // mock the header method in our root controller
        $queue = $this->getMock('\GearmanClient', array('doBackground'));
        $queue->expects($this->exactly(2))
                 ->method('doBackground')
                 ->with( $this->equalTo('crawl'), $this->equalTo( json_encode($job1) ) );
    
        $client = new \thinkup\queue\Client($queue);
        $cnt = $client->queueCrawlJobs(array($job1, $job2));
        $this->assertEquals($cnt, 2);
    }
}
