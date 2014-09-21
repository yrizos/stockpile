<?php

namespace Stockpile;

interface BackendInterface extends CacheInterface
{

    public function __construct(array $options = []);

    public function getOptions();

    public function getOption($option);

    public function set($key, $data, $ttl = 0);

    public function get($key);

    public function exists($key);

    public function delete($key);

    public function flush();

    public function getExpires($ttl);

    public function getKey($key);
} 