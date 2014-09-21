<?php

namespace Stockpile\Tests\Backend;

use Stockpile\Stockpile;
use Stockpile\Tests\Common\BackendCommon;

class FilesystemTest extends BackendCommon
{

    private $directory;

    protected function setUp()
    {
        $this->directory = dirname(__FILE__) . "/../../.cache";
        $this->stockpile = Stockpile::factory("Filesystem", [
            "namespace" => $this->namespace,
            "directory" => $this->directory
        ]);
    }

    public function testGetKey()
    {
        $key       = "my key";
        $directory = $this->stockpile->getBackend()->getOption("directory");
        $expected  = $directory . DIRECTORY_SEPARATOR . "my-key.cache";

        $this->assertEquals($expected, $this->stockpile->getKey($key));
    }
}