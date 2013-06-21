<?php

require_once dirname(__FILE__) . '/../../lib/DispatchParent.php';

class APIParentTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    protected function setUp()
    {
        /* include DispatchParent */
        require_once substr(dirname(__FILE__), 0, -10) . '/lib/DispatchParent.php';
        \thinkup\DispatchParent::init();
        $_POST['auth_token'] = \thinkup\DispatchParent::config('API_AUTH_TOKEN');        
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testStub() {
        $this->assertTrue(true);
    }
}