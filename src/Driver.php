<?php

namespace Stockpile;

use Stockpile\Exception\CacheException;
use Stockpile\Exception\InvalidArgumentException;
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

    /**
     * @return string
     */
    public function getName()
    {
        $class = get_class($this);
        $class = explode("\\", $class);
        $class = array_pop($class);

        return strtolower($class);
    }

    /**
     * @return array
     */
    final public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $name
     * @return mixed|null
     */
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

    abstract protected function connect();

    abstract public function exists($key);

    abstract public function set($key, $value, $ttl = null);

    abstract public function get($key);

    abstract public function delete($key);

    abstract public function clear();

    public static function isCurrent($time)
    {
        if (is_int($time)) $time = new \DateTime('@' . $time);

        return
            ($time instanceof \DateTime)
                ? $time > new \DateTime()
                : false;
    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public static function normalizeKey($key)
    {
        if (!is_string($key)) throw new InvalidArgumentException('Cache key must be a string.');

        $key = trim($key);
        $key = preg_replace('/\s+/', ' ', $key);

        if (empty($key)) throw new InvalidArgumentException('Cache key can\'t be empty.');

        return str_replace(' ', '_', $key);
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