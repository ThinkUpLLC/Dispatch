<?php

class PHPUnitExampleTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
    }

    public function testMe() 
	{

        $this->assertEquals(1, 1, "should pass");
        //$this->assertEquals(0, 1, "should fail");
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Right Message
     */
    public function testExceptionHasRightMessage()
    {
        throw new InvalidArgumentException('Right Message');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFailingInclude()
    {
        include 'not_existing_file.php';
    }

    public function testExpectFooActualFoo()
    {
        $this->expectOutputString('foo');
        print 'foo';
    }

    public function testExpectFooActualFooRegex()
    {
        $this->expectOutputRegex('/oo/');
        print 'foo';
    }
}