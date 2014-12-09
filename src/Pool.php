<?php
namespace Stockpile;

use \Psr\Cache\CacheItemInterface as PsrCacheInterface;

class Pool implements PoolInterface
{

    /**
     * @var DriverInterface
     */
    private $driver;

    private $defered = [];

    public function __construct(DriverInterface $driver = null)
    {
        if (is_null($driver)) $driver = Driver::factory('memory');

        $this->setDriver($driver);
    }

    final public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    final public function getDriver()
    {
        return $this->driver;
    }

    public function getItem($key)
    {
        $value = null;
        $hit   = false;

        if ($this->getDriver()->exists($key)) {
            $value = $this->getDriver()->get($key);
            if ($value) $hit = true;
        }

        return new CacheItem($key, $value, null, $hit);
    }

    public function getItems(array $keys = array())
    {
        $result = [];

        foreach ($keys as $key) $result[$key] = $this->getItem($key);

        return $result;
    }

    public function clear()
    {
        return $this->getDriver()->flush();
    }

    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) $this->getDriver()->delete($key);

        return $this;
    }

    public function save(PsrCacheInterface $item)
    {
        $this->getDriver()->set($item->getKey(), $item->get(), $item->getExpiration());

        return $this;
    }

    public function saveDeferred(PsrCacheInterface $item)
    {
        $this->defered[$item->getKey()] = $item;

        return $this;
    }

    public function commit()
    {
        $result = true;
        foreach ($this->defered as $key => $item) {
            $this->save($item);

            unset($this->defered[$key]);
        }

        return empty($this->defered);
    }

    public static function factory($driver, array $options = [])
    {
        if (!($driver instanceof DriverInterface)) $driver = Driver::factory($driver, $options);

        return new Pool($driver);
    }
}