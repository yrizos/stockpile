<?php

namespace StockpileTest\Driver;

use Stockpile\DriverInterface;

abstract class DriverCommon extends \PHPUnit_Framework_TestCase
{

    protected $data = [];

    /**
     * @var DriverInterface
     */
    protected $driver;

    public function setUp()
    {
        $this->data = [
            'namespace 1\key 1' => 1,
            'namespace 1\key 2' => 'Hello world',
            'namespace 2\key 1' => new \DateTime(),
        ];

        $this->driver = $this->getDriver();
    }

    /**
     * @return DriverInterface
     */
    abstract function getDriver();

    public function testSetGet()
    {
        foreach ($this->data as $key => $value) {
            $this->driver->set($key, $value);

            $this->assertTrue($this->driver->exists($key));
            $this->assertEquals($value, $this->driver->get($key));
        }
    }

    public function testDelete()
    {
        foreach ($this->data as $key => $value) {
            $this->driver->set($key, $value);

            $this->assertTrue($this->driver->exists($key));
            $this->driver->delete($key);
            $this->assertFalse($this->driver->exists($key));
        }
    }

    public function testFlush()
    {
        foreach ($this->data as $key => $value) {
            $this->driver->set($key, $value);

            $this->assertTrue($this->driver->exists($key));
        }

        $this->driver->flush();

        foreach ($this->data as $key => $value) {
            $this->assertFalse($this->driver->exists($key));
        }
    }

} 