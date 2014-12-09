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
        return isset($this->items[self::normalizeKey($key)]);
    }

    public function get($key)
    {
        if (!$this->exists($key)) return null;

        $key   = self::normalizeKey($key);
        $value = $this->items[self::normalizeKey($key)];

        return
            $this->current($value[1])
                ? $value[0]
                : false;
    }

    public function set($key, $value, $ttl = null)
    {
        $key = self::normalizeKey($key);
        $ttl = self::normalizeTtl($ttl);

        $this->items[$key] = [$value, $ttl];

        return $this;
    }

    public function delete($key)
    {
        $key = self::normalizeKey($key);

        unset($this->items[$key]);

        return !$this->exists($key);
    }

}