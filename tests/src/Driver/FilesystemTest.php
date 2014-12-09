<?php
namespace StockpileTest\Driver;

use Stockpile\Driver;

class FilesystemTest extends DriverCommon
{

    public function getDriver()
    {
        return Driver::factory('filesystem', ['directory' => __DIR__ . '/../../.cache']);
    }

    public function testGetName()
    {
        $this->assertEquals('filesystem', $this->driver->getName());
    }

}