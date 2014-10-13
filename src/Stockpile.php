<?php

namespace Stockpile;

class Stockpile implements CacheInterface
{

    private $backend;

    public function __construct(BackendInterface $backend)
    {
        $this->setBackend($backend);
    }

    public function setBackend(BackendInterface $backend)
    {
        $this->backend = $backend;
    }

    public function getBackend()
    {
        return $this->backend;
    }

    public static function factory($backend, array $options = [])
    {
        if (strpos("Stockpile\\Backend\\", $backend) !== 0) {
            $backend = "Stockpile\\Backend\\" . ucfirst($backend);
        }

        if (!class_exists($backend)) throw new \InvalidArgumentException("Backend " . $backend . " doesn't exist");

        return new Stockpile(new $backend($options));
    }

    public function set($key, $data, $ttl = null)
    {
        return $this->backend->set($key, $data, $ttl);
    }

    public function store($key, $data, $ttl = null)
    {
        return $this->set($key, $data, $ttl);
    }

    public function get($key)
    {
        return $this->backend->get($key);
    }

    public function fetch($key)
    {
        return $this->get($key);
    }

    public function exists($key)
    {
        return $this->backend->exists($key);
    }

    public function delete($key)
    {
        return $this->backend->delete($key);
    }

    public function flush()
    {
        return $this->backend->flush();
    }

    public function getExpires($ttl = null)
    {
        return $this->backend->expires($ttl);
    }

    public function getKey($key)
    {
        return $this->backend->getKey($key);
    }

}