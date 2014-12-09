<?php

namespace Stockpile\Exception;

use Psr\Cache\CacheException as PsrCacheException;

class CacheException extends \RuntimeException implements PsrCacheException
{

} 