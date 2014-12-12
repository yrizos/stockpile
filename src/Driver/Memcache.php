<?php

namespace Stockpile\Driver;

use Stockpile\Driver;
use Stockpile\Exception\CacheException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Memcache extends Driver
{
    /**
     * @var \Memcache
     */
    private $memcache;

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'host'    => '127.0.0.1',
            'port'    => (int) ini_get('memcache.default_port'),
            'timeout' => 1,
            'prefix'  => 'stockpile'
        ]);


        $resolver->setAllowedTypes('host', 'string');
        $resolver->setAllowedTypes('port', 'int');
        $resolver->setAllowedTypes('timeout', 'int');
        $resolver->setAllowedTypes('prefix', 'string');

        $resolver->setNormalizers([
            'prefix' => function (Options $options, $prefix) {
                    $prefix = Driver::normalizeKey($prefix);
                    $prefix = trim($prefix, '-') . '-';

                    return $prefix;
                },
        ]);
    }

    protected function connect()
    {
        set_error_handler(function ($errno, $errstr) {
            throw new CacheException('Connection failed: ' . $errstr);
        });

        $this->memcache = new \Memcache($this->getOption('host'), $this->getOption('port'), $this->getOption('timeout'));

        restore_error_handler();
    }

    public function exists($key)
    {
        return false !== $this->get($key);
    }

    public function set($key, $value, $ttl = null)
    {
        $key = $this->getOption('prefix') . Driver::normalizeKey($key);
        $ttl = Driver::normalizeTtl($ttl)->getTimestamp();

        return true === $this->memcache->set($key, $value, 0, $ttl);
    }

    public function get($key)
    {
        $key = $this->getOption('prefix') . Driver::normalizeKey($key);

        return $this->memcache->get($key);
    }

    public function delete($key)
    {
        $key = $this->getOption('prefix') . Driver::normalizeKey($key);

        return true === $this->memcache->delete($key);
    }

    public function clear()
    {
        return true === $this->memcache->flush();
    }
} 