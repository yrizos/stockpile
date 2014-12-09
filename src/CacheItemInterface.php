<?php
namespace Stockpile;

use \Psr\Cache\CacheItemInterface as PsrCacheInterface;

interface CacheItemInterface extends PsrCacheInterface
{

    public function isMiss();

} 