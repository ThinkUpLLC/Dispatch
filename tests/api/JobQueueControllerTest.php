<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';

class JobQueueControllerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /* include DispatchParent */
        require_once substr(dirname(__FILE__), 0, -10) . '/lib/DispatchParent.php';
        \thinkup\DispatchParent::init();
        $_POST['auth_token'] = \thinkup\DispatchParent::config('API_AUTH_TOKEN');        
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($_POST);
        unset($_GET);
        parent::tearDown();
    }

    public function testEmptyQueueRequest() {
        $ctl = new \thinkup\api\JobQueueController();
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->message, "Invalid request: no job queue data in request");
    }


    public function testMalformedJsonRequest() {
        $ctl = new \thinkup\api\JobQueueController();
        $_POST['jobs'] = 'this is bad json}';
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->message, "Invalid request: Crawl jobs is not a valid list");
    }

    public function testBadJsonRequest() {
        $ctl = new \thinkup\api\JobQueueController();
        $_POST['jobs'] = '{"message": "not an array"}';
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->message, "Invalid request: Crawl jobs is not a valid list");

        $_POST['jobs'] = '[{"message": "I am not a job"}]';
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->message, "Invalid request: Crawl Jobs need a 'installation_name', timezone', 'db_name', 'db_host' and 'db_port' or a 'db_socket'");
    }

    public function testValidJsonRequest() {
        // mock the header method in our root controller
        $queue = $this->getMock('\GearmanClient', array('doBackground'));
        $job = '{"installation_name":"mwilkie","timezone":"America\/Los_Angeles","db_host":"localhost","db_name":"thinkup_20120911","db_socket":"\/tmp\/mysql.sock","db_port":""}';
        $_POST['jobs'] = '[' . $job . ']';
        $queue->expects($this->once())
                 ->method('doBackground')
                 ->with( $this->equalTo('crawl'), $this->equalTo( $job ) );

        $ctl = new \thinkup\api\JobQueueController($queue);

        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->success, "1 Job(s) Queued");
    }

    public function testValidJsonRequestHighPriority() {
        // mock the header method in our root controller
        $queue = $this->getMock('\GearmanClient', array('doHighBackground'));
        $job = '{"installation_name":"mwilkie","timezone":"America\/Los_Angeles","db_host":"localhost",' . 
            '"db_name":"thinkup_20120911","db_socket":"\/tmp\/mysql.sock","db_port":"","high_priority":true}';
        $_POST['jobs'] = '[' . $job . ']';
        $queue->expects($this->once())
                 ->method('doHighBackground')
                 ->with( $this->equalTo('crawl'), $this->equalTo( $job ) );

        $ctl = new \thinkup\api\JobQueueController($queue);

        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->success, "1 Job(s) Queued");
    }
}