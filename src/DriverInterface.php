<?php

namespace Stockpile;

interface DriverInterface extends CacheInterface
{
    public function __construct(array $options = []);

    public function getName();

    public function getOptions();

    public function getOption($name);


}