<?php

namespace Stockpile\Tests\Backend;

use Stockpile\Stockpile;
use Stockpile\Tests\Common\BackendCommon;

class MemcacheTest extends BackendCommon
{

    protected function setUp()
    {
        $this->stockpile = Stockpile::factory("Memcache", ["namespace" => $this->namespace]);
    }

}