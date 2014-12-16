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

} 