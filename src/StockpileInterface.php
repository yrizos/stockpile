<?php

namespace Stockpile;

interface StockpileInterface extends CacheInterface
{

    public function __construct($driver, array $options = []);

    public function setDriver(DriverInterface $driver);

    public function getDriver();

} 