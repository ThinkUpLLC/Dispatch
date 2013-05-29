<?php

class GearManTest extends PHPUnit_Framework_TestCase
{

    public function testMe() 
	{

        $this->assertEquals(1, 1, "should pass");
        $this->assertEquals(0, 1, "should fail");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testException()
    {
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Right Message
     */
    public function testExceptionHasRightMessage()
    {
        throw new InvalidArgumentException('Some Message', 10);
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