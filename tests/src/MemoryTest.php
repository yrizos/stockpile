<?php

namespace StockpileTest;

use Stockpile\Driver;

class MemoryTest extends DriverCommon
{

    public function getDriver()
    {
        return Driver::factory('memory');
    }

    public function testGetName()
    {
        $this->assertEquals('memory', $this->driver->getName());
    }

    public function testGetOptions()
    {
        $this->assertEquals([], $this->driver->getOptions());
    }
} 