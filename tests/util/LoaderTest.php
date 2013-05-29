<?php

require_once dirname(__FILE__) . '/../../lib/util/Loader.php';

class LoaderTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Loader::register();
    }

    public function testLoadClass()
    {
        new \thinkup\DispatchParent();
    }


}