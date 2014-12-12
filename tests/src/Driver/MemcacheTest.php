<?php

namespace StockpileTest\Driver;

use Stockpile\Driver;

class MemcacheTest extends DriverCommon
{

    public function getDriver()
    {
        return Driver::factory('memcache');
    }

    public function testGetName()
    {
        $this->assertEquals('memcache', $this->driver->getName());
    }

    public function testGetOptions()
    {
        $this->assertEquals('127.0.0.1', $this->driver->getOption('host'));
        $this->assertEquals(11211, $this->driver->getOption('port'));
        $this->assertEquals(1, $this->driver->getOption('timeout'));
        $this->assertEquals('stockpile-', $this->driver->getOption('prefix'));
    }
} 