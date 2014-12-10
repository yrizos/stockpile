<?php

namespace Stockpile\Driver;

use Stockpile\Driver;
use Stockpile\Exception\CacheException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Filesystem extends Driver
{

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['extension' => 'stockpile', 'directory' => './stockpile']);
        $resolver->setRequired(['directory']);

        $resolver->setNormalizers([
            'extension' => function (Options $options, $extension) {
                    $extension = Driver::normalizeKey($extension);
                    $extension = strtolower($extension);
                    $extension = trim($extension, '.');

                    return '.' . $extension;
                },

            'directory' => function (Options $options, $directory) {
                    return self::normalizeKey($directory);
                }
        ]);
    }

    protected function connect()
    {
        $directory = $this->getOption('directory');

        if (!is_dir($directory)) @mkdir($directory, 0777, true);
        if (!is_dir($directory)) throw new \ErrorException("Couldn't create cache directory.");

        return $this;
    }

    public function exists($key)
    {
        return file_exists($this->getPath($key));
    }

    public function set($key, $value, $ttl = null)
    {
        if (is_resource($value)) return false;

        $path    = $this->getPath($key);
        $dirname = dirname($path);

        if (!is_dir($dirname)) @mkdir($dirname, 0777, true);
        if (!is_dir($dirname)) return false;

        $value = serialize([$value, Driver::normalizeTtl($ttl)]);

        return file_put_contents($path, $value, LOCK_EX) !== false;
    }

    /**
     * @param $key
     * @return bool
     *
     * @throws Exception\CacheException
     */
    public function get($key)
    {
        if (!$this->exists($key)) return false;

        $cache = @file_get_contents($this->getPath($key));

        if ($cache === false) return false;

        set_error_handler(function () {
            throw new CacheException('Unserialization failed.');
        });

        $cache = unserialize($cache);

        restore_error_handler();

        return
            is_array($cache)
            && isset($cache[0])
            && isset($cache[1])
            && Driver::isCurrent($cache[1])
                ? $cache[0]
                : false;
    }

    public function delete($key)
    {
        unlink($this->getPath($key));

        return $this->exists($key) === false;
    }

    public function clear()
    {
        $directory = $this->getOption('directory');
        if (is_dir($directory)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileinfo) {
                $function = ($fileinfo->isDir() ? "rmdir" : "unlink");
                $path     = $fileinfo->getRealPath();
                $function($path);
            }
        }

        return true;
    }

    public function getPath($key)
    {
        $key = self::normalizeKey($key);

        return $this->getOption('directory') . DIRECTORY_SEPARATOR . $key . $this->getOption('extension');
    }

    public static function normalizeKey($key)
    {
        $key = Driver::normalizeKey($key);
        $key = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $key);
        $key = trim($key, DIRECTORY_SEPARATOR);
        $key = explode(DIRECTORY_SEPARATOR, $key);

        $key = array_filter($key, function ($value) {
            return !empty($value);
        });

        $key = array_map(function ($value) {
            if (filter_var($value, FILTER_SANITIZE_STRING) === $value) {
                return $value;
            }

            return md5($value);
        }, $key);

        return implode($key, DIRECTORY_SEPARATOR);
    }
} 