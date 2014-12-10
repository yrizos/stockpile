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

    abstract function getDriver();

    public function setUp()
    {
        for ($i = 0; $i < 5; $i++) $this->data['key ' . $i] = 'value ' . $i;

        $this->driver = $this->getDriver();
    }

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

    public function testClear()
    {
        foreach ($this->data as $key => $value) {
            $this->driver->set($key, $value);

            $this->assertTrue($this->driver->exists($key));
        }

        $this->driver->clear();

        foreach ($this->data as $key => $value) {
            $this->assertFalse($this->driver->exists($key));
        }
    }

    public function testExpire()
    {
        $this->driver->set('expired', '', -1);

        $this->assertTrue($this->driver->exists('expired'));
        $this->assertFalse($this->driver->get('expired'));
    }

//    public function tearDown()
//    {
//        $this->driver->clear();
//    }
} 