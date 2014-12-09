<?php

namespace StockpileTest;

use Stockpile\Cache;
use Stockpile\CacheInterface;

class CacheTest extends \PHPUnit_Framework_TestCase
{

    private $data = [];

    /**
     * @var CacheInterface
     */
    private $cache;

    public function setUp()
    {
        for ($i = 1; $i < 5; $i++) $this->data['key ' . $i] = 'value ' . $i;

        $this->cache = new Cache('filesystem', ['directory' => __DIR__ . '/../.cache']);
    }

    public function testSetGet()
    {
        $this->cache->clear();

        foreach ($this->data as $key => $value) {
            $this->cache->set($key, $value);

            $this->assertEquals($value, $this->cache->get($key));
        }

        $this->assertNull($this->cache->get('unknown'));
    }

    public function testClear()
    {
        $this->cache->clear();

        foreach ($this->data as $key => $value) {
            $this->cache->set($key, $value);

            $this->assertEquals($value, $this->cache->get($key));
        }

        $this->cache->clear();

        foreach ($this->data as $key => $value) {
            $this->assertNull($this->cache->get($key));
        }
    }

    public function tearDown()
    {
        $this->cache->clear();
    }


} 