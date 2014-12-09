<?php

namespace Stockpile;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Driver implements DriverInterface
{

    private $options = [];

    final public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);

        $this->connect();
    }

    protected function configureOptions(OptionsResolver $resolver)
    {

    }

    abstract protected function connect();

    abstract public function flush();

    abstract public function exists($key);

    abstract public function get($key);

    abstract public function set($key, $value);

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

    public static function factory($driver, array $options = [])
    {
        if (strpos($driver, "\\") === false) {
            $driver = ucfirst(strtolower(trim(strval($driver))));
            $driver = "Stockpile\\Driver\\" . $driver;
        }

        if (!class_exists($driver) || !in_array("Stockpile\\DriverInterface", class_implements($driver))) throw new \InvalidArgumentException();

        return new $driver($options);
    }

    public static function serialize($value)
    {
        $value = @serialize($value);

        if (empty($value)) throw new \ErrorException('Serialization failed.');

        return $value;
    }

    public static function unserialize($value)
    {
        set_error_handler(function () {
            throw new \ErrorException('Unserialization failed.');
        });

        $value = unserialize($value);

        restore_error_handler();

        return $value;
    }
} 