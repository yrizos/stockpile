<?php

namespace Stockpile\Backend;

use Predis\Client;
use Stockpile\AbstractBackend;
use Stockpile\BackendInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Redis extends AbstractBackend implements BackendInterface
{

    private $redis;

    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            "scheme" => "tcp",
            "host"   => "127.0.0.1",
            "port"   => 6379,
        ]);
    }

    public function initialize()
    {
        $this->redis = new Client([
            "scheme" => $this->getOption("scheme"),
            "host"   => $this->getOption("host"),
            "port"   => $this->getOption("port"),
        ]);

        return $this;
    }

    public function set($key, $data, $ttl = 0)
    {
        $key  = $this->getKey($key);
        $data = @serialize($data);

        $this->redis->set($key, $data);
        $this->redis->expire($key, $this->getExpires($ttl));

        return true;
    }

    public function get($key)
    {
        if (!$this->exists($key)) return false;

        $data = $this->redis->get($this->getKey($key));
        if ($data) $data = @unserialize($data);

        return !empty($data) ? $data : false;
    }

    public function exists($key)
    {
        if (!$this->redis->exists($this->getKey($key))) return false;

        if ($this->redis->ttl($this->getKey($key)) < 0) {
            $this->delete($key);

            return false;
        }

        return true;
    }

    public function delete($key)
    {
        return $this->redis->del($this->getKey($key));
    }

    public function flush()
    {
        $result = (string)$this->redis->flushdb();

        return $result === "OK";
    }


}