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

    public function testSetGet()
    {
        parent::testSetGet();

        foreach ($this->data as $key => $value) {
            $path = $this->driver->getPath($key);

            $this->assertFileExists($path);
        }
    }

} 