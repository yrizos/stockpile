<?php

namespace Stockpile;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractBackend
{

    private $options = [];
    private $defaultTtl = 0;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        $this->initialize();
    }

    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(["namespace" => "stockpile", "default_ttl" => 0]);
        $resolver->setRequired([]);

        $resolver->setNormalizers([
            "namespace" => function (Options $options, $value) {
                    return self::normalizeKey($value);
                },
            "default_ttl" => function (Options $options, $value) {
                    return (int) $value;
                }
        ]);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($option)
    {
        return
            isset($this->options[$option])
                ? $this->options[$option]
                : null;
    }

    protected function setOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function getExpires($ttl = null)
    {
        if (is_null($ttl)) $ttl = $this->getOption("default_ttl");
        $ttl = (int) $ttl;

        return $ttl;
    }

    public function getKey($key)
    {
        $ns  = $this->getOption("namespace");
        $key = self::normalizeKey($key);

        if ($key === "") throw new \InvalidArgumentException("Cache key can't be empty");
        if (!empty($ns)) $key = $ns . ":" . $key;

        return $key;
    }

    public static function normalizeKey($key)
    {
        return str_replace(" ", "-", preg_replace("/(\\s)+/", " ", trim(strval($key))));
    }

    abstract function initialize();

    abstract function set($key, $data, $ttl = 0);

    abstract function get($key);

    abstract function exists($key);

    abstract function delete($key);

    abstract function flush();

    public function store($key, $data, $ttl = null)
    {
        return $this->set($key, $data, $ttl);
    }

    public function fetch($key)
    {
        return $this->get($key);
    }
} 