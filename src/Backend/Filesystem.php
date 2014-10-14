<?php

namespace Stockpile\Backend;

use Stockpile\AbstractBackend;
use Stockpile\BackendInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Filesystem extends AbstractBackend implements BackendInterface
{
    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(["extension" => "cache", "directory" => "./cache"]);
        $resolver->setRequired(["directory"]);

        $resolver->setNormalizers([
            "extension" => function (Options $options, $extension) {
                    return "." . ltrim(trim(strval($extension)), ".");
                },

            "directory" => function (Options $options, $directory) {
                    $namespace = self::normalizePath($options->get("namespace"));
                    $directory = self::normalizePath($directory);

                    if (!empty($namespace)) $directory .= DIRECTORY_SEPARATOR . $namespace;

                    return $directory;
                }

        ]);
    }

    public function getKey($key)
    {
        $key       = self::normalizePath($key);
        $directory = $this->getOption("directory");
        $extension = $this->getOption("extension");

        return $directory . DIRECTORY_SEPARATOR . $key . $extension;
    }

    public function initialize()
    {
        $directory = $this->getOption("directory");

        if (!is_dir($directory)) @mkdir($directory, 0777, true);
        if (!is_dir($directory) || !is_writable($directory)) throw new \InvalidArgumentException($directory . " must be a writeable directory.");

        $this->setOption("directory", realpath($directory));

        return $this;
    }

    public function set($key, $data, $ttl = null)
    {
        $key = $this->getKey($key);
        $dir = dirname($key);

        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        if (!is_dir($dir)) throw new \RuntimeException();

        $cache = [$data, $this->getExpires($ttl)];
        $cache = @serialize($cache);

        if (!$cache) throw new \RuntimeException();

        $result = @file_put_contents($key, $cache);

        return $result !== false;
    }

    public function get($key)
    {
        if (!$this->exists($key)) return false;

        $key   = $this->getKey($key);
        $cache = @file_get_contents($key);
        $cache = @unserialize($cache);
        if (empty($cache) || !is_array($cache)) return false;

        list($data, $expires) = $cache;

        return
            $expires > $this->getExpires(0)
                ? $data
                : false;
    }

    public function exists($key)
    {
        $key = $this->getKey($key);

        return file_exists($key) && is_readable($key);
    }

    public function delete($key)
    {
        if (!$this->exists($key)) return true;

        unlink($this->getKey($key));

        return !$this->exists($key);
    }

    public function getExpires($ttl = null)
    {
        return time() + parent::getExpires($ttl);
    }

    public function flush()
    {
        return self::removeDirectory($this->getOption("directory"));
    }

    public static function normalizePath($path)
    {
        $path = self::normalizeKey($path);
        $path = rtrim(str_replace(["\\", "/"], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);

        return $path;
    }

    public static function removeDirectory($directory)
    {
        if (!is_dir($directory)) return true;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $function = ($fileinfo->isDir() ? "rmdir" : "unlink");
            $path     = $fileinfo->getRealPath();

            $function($path);
        }

        rmdir($directory);

        return !file_exists($directory);
    }

} 