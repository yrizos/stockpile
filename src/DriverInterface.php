<?php

namespace Stockpile;

interface DriverInterface
{

    public function __construct(array $options = []);

    public function flush();

    public function exists($key);

    public function get($key);

    public function set($key, $value);

    public function delete($key);

    public function getName();

    public function getOptions();

    public function getOption($name);
}