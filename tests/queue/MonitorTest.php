<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';

class MonitorTest extends PHPUnit_Framework_TestCase
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
     * 
     */
    public function testMonitor() {
        $monitor = new \thinkup\queue\Monitor();
        
        //$client->queueCrawlJobs();
        $this->assertEquals('127.0.0.1',$monitor->host);
        $this->assertEquals(4730,$monitor->port);
    }

    /**
     * Failed connection
     * @expectedException PHPUnit_Framework_Error
     */
    public function testMonitorFailedConnection() {
        $monitor = new \thinkup\queue\Monitor();        
        $monitor->port = 47333;
        $status = $monitor->getStatus();
    }

    /**
     */
    public function testMonitorGoodConnection() {
        $with_gearmand = getenv('WITH_GEARMAND');
        if (isset($with_gearmand) && $with_gearmand == 1) {
            $monitor = new \thinkup\queue\Monitor();
            $status = $monitor->getStatus();
            $this->assertArrayHasKey('operations', $status);
            $this->assertArrayHasKey('connections', $status);
        } else {
            $this->assertTrue(true, 'Skip gearmand server test');
        }   
    }
}