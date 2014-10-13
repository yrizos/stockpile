<?php

namespace Stockpile;

trait StockpileTrait
{

    private $cache;

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function cacheSet($key, $data, $ttl = null)
    {
        return
            $this->getCache()
                ? $this->cache->set($key, $data, $ttl)
                : false;
    }

    public function cacheGet($key)
    {
        return
            $this->getCache()
                ? $this->getCache()->get($key)
                : null;
    }

} 