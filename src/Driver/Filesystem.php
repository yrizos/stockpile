<?php

namespace Stockpile\Driver;

use Stockpile\Driver;
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
                    return trim(trim(strval($extension)), '.');
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

    public function flush()
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

    public function exists($key)
    {
        $path = $this->getPath($key);

        return file_exists($path) && is_readable($path);
    }

    public function get($key)
    {
        if (!$this->exists($key)) return null;

        try {
            $value = Driver::unserialize(file_get_contents($this->getPath($key)));
        } catch (\Exception $e) {
            return null;
        }

        return $value;
    }

    public function set($key, $value)
    {
        $path    = $this->getPath($key);
        $dirname = dirname($path);
        $value   = Driver::serialize($value);

        if (!is_dir($dirname)) @mkdir($dirname, 0777, true);
        if (!is_dir($dirname)) throw new \ErrorException("Couldn't create cache directory.");

        if (file_put_contents($path, $value) === false) throw new \ErrorException("Couldn't save cache file.");

        return $this;
    }

    public function delete($key)
    {
        if ($this->exists($key)) unlink($this->getPath($key));

        return !$this->exists($key);
    }

    protected function getPath($key)
    {
        $key = self::normalizeKey($key);

        return $this->getOption('directory') . DIRECTORY_SEPARATOR . $key . '.' . $this->getOption('extension');
    }

    public static function normalizeKey($key)
    {
        $key = trim(strval($key));
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

} 