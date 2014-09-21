<?php

namespace Stockpile\Backend;

use Stockpile\AbstractBackend;
use Stockpile\BackendInterface;

class Memory extends AbstractBackend implements BackendInterface
{

    private $storage = [];

    public function getExpires($ttl)
    {
        return time() + parent::getExpires($ttl);
    }

    public function initialize()
    {
    }

    public function set($key, $data, $ttl = 0)
    {
        $this->storage[$this->getKey($key)] = [$data, $this->getExpires($ttl)];

        return true;
    }

    public function get($key)
    {
        if (!$this->exists($key)) return false;

        list($data, $expires) = $this->storage[$this->getKey($key)];

        return
            $expires > $this->getExpires(0)
                ? $data
                : false;
    }

    public function exists($key)
    {
        return isset($this->storage[$this->getKey($key)]);
    }

    public function delete($key)
    {
        if (!$this->exists($key)) return true;

        unset($this->storage[$this->getKey($key)]);

        return !$this->exists($key);
    }

    public function flush()
    {
        $this->storage = [];

        return true;
    }
} 