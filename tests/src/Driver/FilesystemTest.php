<?php
namespace StockpileTest\Driver;

use Stockpile\Driver;
use Stockpile\DriverInterface;

class MemoryTest extends DriverCommon
{

    /**
     * @var DriverInterface
     */
    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = Driver::factory('memory', []);
    }

    public function testGetName()
    {
        $this->assertEquals('memory', $this->driver->getName());
    }

    public function testSetGet()
    {
        foreach ($this->data as $key => $value) {
            $this->driver->set($key, $value);

            $this->assertSame($value, $this->driver->get($key));
        }
    }

}