<?php

namespace Stockpile\Driver;

use Stockpile\Driver;
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
            'timeout' => 1
        ]);
    }

    protected function connect()
    {
        $this->memcache = new \Memcache();
        $this->memcache->connect($this->getOption('host'), $this->getOption('port'), $this->getOption('timeout'));
    }

    public function exists($key)
    {
        return false !== $this->get($key);
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_resource($value)) return false;

        return true === $this->memcache->set(self::normalizeKey($key), $value, 0, self::normalizeTtl($ttl));

    }

    public function get($key)
    {
        return $this->memcache->get(self::normalizeKey($key));
    }

    public function delete($key)
    {
        return true === $this->memcache->delete(self::normalizeKey($key));
    }

    public function clear()
    {
        return true === $this->memcache->flush();
    }

    public static function normalizeTtl($ttl = null)
    {
        if (!is_int($ttl)) return 0;

        return (int) $ttl;
    }
}