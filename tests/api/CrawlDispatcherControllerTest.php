<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';

class CrawlDispatcherController extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /* include DispatchParent */
        require_once substr(dirname(__FILE__), 0, -10) . '/lib/DispatchParent.php';
        \thinkup\DispatchParent::init();
        
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($_POST);
        unset($_GET);
        parent::tearDown();
    }

    public function testBadAuth() {
        // mock the header method in our root controller
        $ctl = $this->getMock('\thinkup\api\CrawlDispatcherController', array('header'));
        $ctl->expects($this->any())
                 ->method('header')
                 ->with($this->equalTo('HTTP/1.0 401 Unauthorized'));

        // no auth token
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->error, 'Invalid Auth');

        // a bad auth token
        $_GET['auth_token'] = 'this is a bad token';
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->error, 'Invalid Auth');

    }

    public function testValidAuth() {
        $ctl = new \thinkup\api\CrawlDispatcherController();
        $_GET['auth_token'] = \thinkup\DispatchParent::config('API_AUTH_TOKEN');
        $json = $ctl->execute();
        $obj = json_decode($json);
        $this->assertEquals($obj->message, 'This method should be overridden by a child class');
    }
}