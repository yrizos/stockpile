<?php

namespace Stockpile;

interface CacheInterface
{

    public function set($key, $data, $ttl = 0);

    public function store($key, $data, $ttl = 0);

    public function get($key);

    public function fetch($key);

    public function exists($key);

    public function delete($key);

    public function flush();

    public function getExpires($ttl);

    public function getKey($key);

} 