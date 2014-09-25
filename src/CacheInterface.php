<?php

namespace Stockpile;

interface CacheInterface
{
    public function set($key, $data, $ttl = null);

    public function store($key, $data, $ttl = null);

    public function get($key);

    public function fetch($key);

    public function exists($key);

    public function delete($key);

    public function flush();

    public function getExpires($ttl = null);

    public function getKey($key);
} 