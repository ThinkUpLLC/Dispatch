<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';
require_once dirname(__FILE__) . '/../data/MonitorData.php';
require_once dirname(__FILE__) . '/../model/CrawlFixtureDataSet.php';
require_once dirname(__FILE__) . '/../model/ModelTest.php';

class MonitorControllerTest extends Modeltest
{
    public function setUp()
    {
        /* include DispatchParent */
        require_once substr(dirname(__FILE__), 0, -10) . '/lib/DispatchParent.php';
        \thinkup\DispatchParent::init();
        $_POST['auth_token'] = \thinkup\DispatchParent::config('API_AUTH_TOKEN');        
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
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

    public function testGetCrawlStatus() {

        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));

        $monitor->expects($this->any())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

        $monitor_ctl = new thinkup\api\MonitorController($monitor);

        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $this->assertEquals(2, sizeof($resonse_data['crawl_status']));
        $this->assertEquals(1, $resonse_data['crawl_status'][0]['count']);
        $this->assertEquals(5, $resonse_data['crawl_status'][1]['count']);

        // filter by install name
        $_GET['install_name'] = 'test 1';
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $this->assertEquals(2, sizeof($resonse_data['crawl_status']));
        $this->assertEquals(1, $resonse_data['crawl_status'][0]['count']);
        $this->assertEquals(3, $resonse_data['crawl_status'][1]['count']);

        $_GET['install_name'] = 'test 2';
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $this->assertEquals(1, sizeof($resonse_data['crawl_status']));
        $this->assertEquals(2, $resonse_data['crawl_status'][0]['count']);

        $_GET['install_name'] = 'test 3'; // bad install name
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $this->assertEquals(0, sizeof($resonse_data['crawl_status']));
    }

    public function testGetCrawlData() {
        
        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));
        
        $monitor->expects($this->any())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

        $monitor_ctl = new thinkup\api\MonitorController($monitor);

        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $this->assertEquals(6, sizeof($resonse_data['crawl_data']));
        $data = $resonse_data['crawl_data'];
        $this->assertEquals(6, sizeof($data));
        $this->assertEquals('test 1', $data[5]['install_name']);
        $this->assertEquals(1, $data[5]['id']);
        $this->assertEquals(1, $data[5]['crawl_status']);
        $this->assertEquals(140, $data[5]['crawl_time']);
        $this->assertEquals('test 2', $data[3]['install_name']);
        $this->assertEquals(3, $data[3]['id']);
        
        // filter by install name
        $_GET['install_name'] = 'test 1';
        // for test 1 install
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        #var_dump($resonse_data);
        $data = $resonse_data['crawl_data'];
        $this->assertEquals(4, sizeof($data));
        $this->assertEquals('test 1', $data[3]['install_name']);
        $this->assertEquals(1, $data[3]['id']);
        $this->assertEquals(1, $data[3]['crawl_status']);
        $this->assertEquals(140, $data[3]['crawl_time']);
        $this->assertEquals('test 1', $data[3]['install_name']);
        $this->assertEquals(1, $data[3]['id']);

        // for test 2 install
        $_GET['install_name'] = 'test 2';
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $data = $resonse_data['crawl_data'];
        $this->assertEquals(2, sizeof($data));
        $this->assertEquals('test 2', $data[1]['install_name']);
        $this->assertEquals(3, $data[1]['id']);
        $this->assertEquals(1, $data[1]['crawl_status']);
        $this->assertEquals(10, $data[1]['crawl_time']);
        $this->assertEquals('test 2', $data[0]['install_name']);
        $this->assertEquals(5, $data[0]['id']);

        // for test 3 install bad install name
        $_GET['install_name'] = 'test 3';
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $data = $resonse_data['crawl_data'];
        $this->assertEquals(0, sizeof($data));

    }

    public function testGetLogData() {
        
        // mock Queue Monitor getStatus()
        $monitor = $this->getMock('\thinkup\queue\Monitor', array('getStatus'));        
        $monitor->expects($this->any())
                 ->method('getStatus')
                 ->will($this->returnValue( MonitorData::getMonitorData() ));

        $monitor_ctl = new thinkup\api\MonitorController($monitor);

        $_GET['log'] = '1';
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 

        $this->assertEquals(1, $resonse_data['id']);
        $this->assertEquals(1, $resonse_data['crawl_status_id']);
        $this->assertEquals('crawl log id 1', $resonse_data['crawl_log']);

        $_GET['log'] = '-1'; // bad id
        $json = $monitor_ctl->execute();
        $this->assertNotNull($json);
        $resonse_data = json_decode($json, true); 
        $this->assertNull($resonse_data);

   }


}