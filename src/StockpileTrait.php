<?php

namespace Stockpile;

trait StockpileTrait
{

    private $cache;
    private $flag = true;

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache()
    {
        return $this->flag ? $this->cache : false;
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

    public function cacheOn()
    {
        $this->flag = true;

        return $this;
    }

    public function cacheOff()
    {
        $this->flag = false;

        return $this;
    }
} 