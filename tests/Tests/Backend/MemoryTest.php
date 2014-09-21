<?php

namespace Stockpile\Tests\Backend;

use Stockpile\Stockpile;
use Stockpile\Tests\Common\BackendCommon;

class MemoryTest extends BackendCommon
{

    protected function setUp()
    {
        $this->stockpile = Stockpile::factory("Memory", ["namespace" => $this->namespace]);
    }

}