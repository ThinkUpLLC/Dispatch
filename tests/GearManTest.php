<?php

class GearManTest extends PHPUnit_Framework_TestCase
{

    public function testMe() {

        $this->assertEquals(1, 1, "should pass");
        $this->assertEquals(0, 1, "should fail");
    }
}