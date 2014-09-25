<?php

namespace Stockpile\Backend;

use Stockpile\AbstractBackend;
use Stockpile\BackendInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Memcache extends AbstractBackend implements BackendInterface
{
    private $memcache;

    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            "host" => "127.0.0.1",
            "port" => 11211,
        ]);
    }

    public function initialize()
    {
        if (!extension_loaded("memcache")) throw new \RuntimeException();

        $this->memcache = new \Memcache;
        $this->memcache->addserver($this->getOption("host"), $this->getOption("port"));
    }

    public function set($key, $data, $ttl = null)
    {
        return $this->memcache->set($this->getKey($key), $data, 0, $this->getExpires($ttl));
    }

    public function get($key)
    {
        return $this->memcache->get($this->getKey($key));
    }

    public function exists($key)
    {
        return $this->get($key) !== false;
    }

    public function delete($key)
    {
        return $this->memcache->delete($this->getKey($key));
    }

    public function flush()
    {
        return $this->memcache->flush();
    }

    public function __destruct()
    {
        $this->memcache->close();
    }
} 