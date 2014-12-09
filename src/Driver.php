<?php

namespace Stockpile;

use Stockpile\Exception\CacheException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Driver implements DriverInterface
{

    const DEFAULT_EXPIRATION = 'now +1 month';

    private $options = [];

    final public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        $this->connect();
    }

    abstract protected function connect();

    abstract public function flush();

    abstract public function exists($key);

    abstract public function get($key);

    abstract public function set($key, $value, $ttl = null);

    abstract public function delete($key);

    public function getName()
    {
        $class = get_class($this);
        $class = explode("\\", $class);
        $class = array_pop($class);

        return strtolower($class);
    }

    final public function getOptions()
    {
        return $this->options;
    }

    final public function getOption($name)
    {
        return
            isset($this->options[$name])
                ? $this->options[$name]
                : null;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {

    }

    protected function current($expiration)
    {
        if (is_int($expiration)) $expiration = new \DateTime('@' . $expiration);
        if (!($expiration instanceof \DateTime)) return false;

        $now = new \DateTime();

        return $expiration > $now;
    }

    public function normalizeKey($key)
    {
        if (!is_string($key)) throw new \InvalidArgumentException();

        $key = trim($key);
        $key = str_replace(' ', '_', $key);
        $key = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $key);
        $key = trim($key, DIRECTORY_SEPARATOR);
        $key = explode(DIRECTORY_SEPARATOR, $key);

        $key = array_map(function ($value) {
            if (filter_var($value, FILTER_SANITIZE_STRING) === $value) {
                return $value;
            }

            return md5($value);
        }, $key);

        return implode(DIRECTORY_SEPARATOR, $key);
    }

    public static function normalizeTtl($ttl = null)
    {
        if (is_int($ttl)) {
            $ttl = $ttl + time();
            $ttl = new \DateTime('@' . $ttl);
        }

        if (!($ttl instanceof \DateTime)) $ttl = new \DateTime(self::DEFAULT_EXPIRATION);

        return $ttl;
    }

    public static function factory($driver, array $options = [])
    {
        if (strpos($driver, "\\") === false) {
            $driver = ucfirst(strtolower(trim(strval($driver))));
            $driver = "Stockpile\\Driver\\" . $driver;
        }

        if (!class_exists($driver) || !in_array("Stockpile\\DriverInterface", class_implements($driver))) throw new CacheException('Cache driver is invalid.');

        return new $driver($options);
    }


} 