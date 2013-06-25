<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';
require_once dirname(__FILE__) . '/../data/MonitorData.php';

class MonitorControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /* include DispatchParent */
        require_once substr(dirname(__FILE__), 0, -10) . '/lib/DispatchParent.php';
        \thinkup\DispatchParent::init();
        $_POST['auth_token'] = \thinkup\DispatchParent::config('API_AUTH_TOKEN');        
        parent::setUp();
    }

    public function testDefaultActionGetStatusOK() {
        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));
        $monitor->expects($this->once())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

        $monitor_ctl = new thinkup\api\MonitorController($monitor);
        $json = $monitor_ctl->execute();
        $obj = json_decode($json, true);
        $this->assertNotNull($obj);
        //var_dump($obj);
        $this->assertArrayHasKey('operations', $obj['gearman_status']);
        $this->assertArrayHasKey('connections', $obj['gearman_status']);
        $this->assertArrayHasKey('crawl', $obj['gearman_status']['operations']);
        $this->assertEquals(true, $obj['gearman_ok']);
        $cnt = thinkup\api\MonitorController::$config->get('CONNECTED_WORKERS');
        $this->assertEquals($cnt, $obj['workers_wanted']);
    }

    public function testDefaultActionGetStatusNotOK() {
        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));
        $monitor->expects($this->once())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

        $monitor_ctl = new thinkup\api\MonitorController($monitor);

        $cnt = thinkup\api\MonitorController::$config->get('CONNECTED_WORKERS');
        thinkup\api\MonitorController::$config->set('CONNECTED_WORKERS', $cnt + 1);
        $json = $monitor_ctl->execute();;
        thinkup\api\MonitorController::$config->set('CONNECTED_WORKERS', $cnt); // reset
        $obj = json_decode($json, true);
        $this->assertNotNull($obj);
        $this->assertArrayHasKey('operations', $obj['gearman_status']);
        $this->assertArrayHasKey('connections', $obj['gearman_status']);
        $this->assertArrayHasKey('crawl', $obj['gearman_status']['operations']);
        $this->assertEquals(false, $obj['gearman_ok']);
    }

    public function testNagiosStatusGood() {

        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));        
        $monitor->expects($this->once())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

         $monitor_ctl = new thinkup\api\MonitorController($monitor);
         $_GET['nagios_check'] = 1;

        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true);
        $this->assertEquals('1 running worker(s) - OK', $resonse_data['status']);

    }

    public function testNagiosStatusNotGood() {

        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));
        
        $monitor->expects($this->once())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

        $monitor_ctl = new thinkup\api\MonitorController($monitor);
        $_GET['nagios_check'] = 1;

        $cnt = thinkup\api\MonitorController::$config->get('CONNECTED_WORKERS');
        thinkup\api\MonitorController::$config->set('CONNECTED_WORKERS', $cnt + 1);
        $json = $monitor_ctl->execute();
        thinkup\api\MonitorController::$config->set('CONNECTED_WORKERS', $cnt - 1); // reset
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true);
        $this->assertEquals('2 running worker(s) found - NOT OK', $resonse_data['status']);

    }


}