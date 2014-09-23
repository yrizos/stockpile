<?php

namespace Stockpile;

interface BackendInterface extends CacheInterface
{

    public function __construct(array $options = []);

    public function getOptions();

    public function getOption($option);

} 