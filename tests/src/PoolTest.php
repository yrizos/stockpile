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
            $this->pool->save($item);
        }

        $items = $this->pool->getItems(array_keys($this->data));
        foreach ($item as $item) $this->assertTrue($item->isMiss());

        $this->pool->commit();

        $items = $this->pool->getItems(array_keys($this->data));
        foreach ($item as $item) $this->assertTrue($item->isHit());
    }

    public function tearDown()
    {
        $this->pool->clear();
    }

} 