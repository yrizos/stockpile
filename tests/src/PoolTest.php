<?php

namespace StockpileTest;

use Stockpile\CacheItem;
use Stockpile\Driver;
use Stockpile\Pool;
use Stockpile\PoolInterface;

class PoolTest extends \PHPUnit_Framework_TestCase
{

    private $data = [];

    /**
     * @var PoolInterface
     */
    private $pool;

    public function setUp()
    {
        for ($i = 1; $i < 5; $i++) $this->data['key ' . $i] = 'value ' . $i;
        $this->pool = Pool::factory('filesystem', ['directory' => __DIR__ . '/../.cache']);
    }

    public function testNullDriver()
    {
        $pool = new Pool();
        $this->assertEquals('memory', $pool->getDriver()->getName());
    }

    public function testFactory()
    {
        $driver = Driver::factory('filesystem', ['directory' => __DIR__ . '/../.cache']);
        $pool   = Pool::factory($driver);

        $this->assertEquals('filesystem', $pool->getDriver()->getName());

        $pool = Pool::factory('filesystem', ['directory' => __DIR__ . '/../.cache']);

        $this->assertEquals('filesystem', $pool->getDriver()->getName());
    }

    public function testSave()
    {
        $this->pool->clear();

        foreach ($this->data as $key => $value) {
            $item = new CacheItem($key, $value);

            $this->pool->save($item);

            $item = $this->pool->getItem($key);

            $this->assertTrue($item->isHit());
            $this->assertEquals($value, $item->get());
        }
    }

    public function testDeferred()
    {
        $this->pool->clear();

        foreach ($this->data as $key => $value) {
            $item = new CacheItem($key, $value);

            $this->pool->saveDeferred($item);

            $item = $this->pool->getItem($key);

            $this->assertFalse($item->isHit());
        }

        $this->pool->commit();

        foreach ($this->data as $key => $value) {
            $item = $this->pool->getItem($key);

            $this->assertTrue($item->isHit());
            $this->assertEquals($value, $item->get());
        }
    }

    public function testDestruct()
    {
        $pool = Pool::factory('filesystem', ['directory' => __DIR__ . '/../.cache']);

        foreach ($this->data as $key => $value) {
            $item = new CacheItem($key, $value);

            $pool->saveDeferred($item);

            $item = $pool->getItem($key);

            $this->assertFalse($item->isHit());
        }

        unset($pool);

        $pool = Pool::factory('filesystem', ['directory' => __DIR__ . '/../.cache']);

        foreach ($this->data as $key => $value) {
            $item = $pool->getItem($key);

            $this->assertTrue($item->isHit());
            $this->assertEquals($value, $item->get());
        }
    }

    public function tearDown()
    {
        $this->pool->clear();
    }


} 