<?php

require_once dirname(__FILE__) . '/../lib/DispatchParent.php';

class DispatcheParentTest extends PHPUnit_Framework_TestCase
{

    public function testNewDispatchParent()
    {
        $cmo = new thinkup\DispatchParent();
        $this->assertInstanceOf('thinkup\DispatchParent', $cmo);
        $this->assertTrue( $cmo->isLoaderRegistered(), 'our loader should be registered');
    }

    public function testConfig()
    {
        $cmo = new thinkup\DispatchParent();
        $this->assertNull($cmo->config('bad_key'));
        $this->assertRegExp('/\d+\.\d+/', $cmo->config('API_VERSION'));
    }
    
}