<?php

namespace Stockpile;

interface CacheInterface
{

    public function __construct($driver, array $options = []);

    public function set($key, $value, $ttl = null);

    public function get($key);

    public function clear();
} 