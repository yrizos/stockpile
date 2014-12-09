<?php
namespace Stockpile;

use Psr\Cache\CacheItemPoolInterface as PsrCacheItemPoolInterface;

interface PoolInterface extends PsrCacheItemPoolInterface
{

    public function __construct(DriverInterface $driver = null);

    public function setDriver(DriverInterface $driver);

    public function getDriver();

} 