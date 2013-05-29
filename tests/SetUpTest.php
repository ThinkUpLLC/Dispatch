<?php

class SetUpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Make sure Gearman pecl extension is installed
     */
    public function testGearmanExtension() 
    {
        $client = new GearmanClient();
        $this->assertInstanceOf('GearmanClient', $client);
    }

}
