<?php

namespace Stockpile\Driver;

use Stockpile\Driver;

class Memory extends Driver
{
    private $storage = [];

    protected function connect()
    {

    }

    public function exists($key)
    {
        return isset($this->storage[self::normalizeKey($key)]);
    }

    public function set($key, $value, $ttl = null)
    {
        $key = self::normalizeKey($key);
        $ttl = self::normalizeTtl($ttl);

        $this->storage[$key] = [$value, $ttl];

        return $this;
    }

    public function get($key)
    {
        if (!$this->exists($key)) return false;

        $cache = $this->storage[self::normalizeKey($key)];

        return
            self::isCurrent($cache[1])
                ? $cache[0]
                : false;

    }

    public function delete($key)
    {
        unset($this->storage[self::normalizeKey($key)]);

        return $this;
    }

    public function clear()
    {
        $this->storage = [];

        return $this;
    }

}