<?php

namespace Stockpile\Tests\Backend;

use Stockpile\Stockpile;
use Stockpile\Tests\Common\BackendCommon;

class RedisTest extends BackendCommon
{

    protected function setUp()
    {
        $this->stockpile = Stockpile::factory("Redis", ["namespace" => $this->namespace]);
    }

    public function testGetKey()
    {
        $key       = "my key";
        $expected = "stockpile-test:my-key";

        $this->assertEquals($expected, $this->stockpile->getKey($key));
    }
}