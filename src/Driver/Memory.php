<?php

namespace Stockpile\Driver;

use Stockpile\Driver;

class Memory extends Driver
{

    private $items = [];

    protected function connect()
    {
        return $this;
    }

    public function flush()
    {
        $this->items = [];

        return $this;
    }

    public function exists($key)
    {



        return isset($this->items[$key]);
    }

    public function get($key)
    {
        return
            $this->exists($key)
                ? $this->items[$key]
                : null;
    }

    public function set($key, $value)
    {
        $this->items[$key] = $value;

        return $this;
    }

    public function delete($key)
    {
        unset($this->items[$key]);

        return !$this->exists($key);
    }

}