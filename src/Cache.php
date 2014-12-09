<?php

namespace Stockpile;

class Cache implements CacheInterface
{

    /**
     * @var PoolInterface
     */
    private $pool;

    public function __construct($driver, array $options = [])
    {
        $this->pool = Pool::factory($driver, $options);
    }

    public function set($key, $value, $ttl = null)
    {
        $item = new CacheItem($key, $value, $ttl);

        $this->pool->save($item);

        return $this;
    }

    public function get($key)
    {
        $item = $this->pool->getItem($key);

        return
            $item->isHit()
                ? $item->get()
                : null;
    }

    public function clear()
    {
        $this->pool->clear();

        return $this;
    }
} 