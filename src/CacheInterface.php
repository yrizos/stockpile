<?php
namespace Stockpile;

interface CacheInterface
{

    public function exists($key);

    public function set($key, $value, $ttl = null);

    public function get($key);

    public function delete($key);

    public function clear();
} 