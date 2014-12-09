<?php
namespace StockpileTest\Driver;

use Stockpile\Driver;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        $driver = Driver::factory('memory', []);

        var_dump($driver);
    }

}