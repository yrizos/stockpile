<?php

namespace Stockpile\Tests\Common;

class BackendCommon extends \PHPUnit_Framework_TestCase
{
    protected $stockpile;
    protected $namespace = "test namespace";
    protected $data =
        [
            "key 1" => ["some", "data"],
            "key 2" => ["hello", "world"],
            "key 3" => "value 3"
        ];

    public function testGetKey()
    {
        $key      = "my key";
        $expected = "test-namespace::my-key";

        $this->assertEquals($expected, $this->stockpile->getKey($key));
    }

    public function testSetGet()
    {
        foreach ($this->data as $key => $value) {
            $result = $this->stockpile->set($key, $value, 5);

            $this->assertTrue($result);
            $this->assertTrue($this->stockpile->exists($key));
            $this->assertEquals($value, $this->stockpile->get($key));

            $this->stockpile->delete($key);
            $this->assertFalse($this->stockpile->exists($key));
            $this->assertFalse($this->stockpile->get($key));
        }
    }

    public function testExpires()
    {
        $key   = "key";
        $value = "value";

        $this->stockpile->set($key, $value, 1);


        $this->assertTrue($this->stockpile->exists($key));
        $this->assertEquals($value, $this->stockpile->get($key));

        sleep(1.1);

        $this->assertFalse($this->stockpile->get($key));
        $this->stockpile->delete($key);
    }

    public function testFlush()
    {
        foreach ($this->data as $key => $value) $this->stockpile->set($key, $value, 5);

        $result = $this->stockpile->flush();
        $this->assertTrue($result);

        foreach ($this->data as $key => $value) {
            $this->assertFalse($this->stockpile->exists($key));
            $this->assertFalse($this->stockpile->get($key));
        }
    }

}