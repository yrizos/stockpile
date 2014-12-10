<?php

namespace Stockpile;

interface DriverInterface
{
    public function __construct(array $options = []);

    public function getName();

    public function getOptions();

    public function getOption($name);

    public function exists($key);

    public function set($key, $value, $ttl = null);

    public function get($key);

    public function delete($key);

    public function clear();
}