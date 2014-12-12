<?php
namespace Stockpile;

class Stockpile implements StockpileInterface
{

    /**
     * @var DriverInterface
     */
    private $driver;

    public function __construct($driver, array $options = [])
    {
        $this->setDriver(Driver::factory($driver, $options));
    }

    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    public function exists($key)
    {
        return $this->driver->exists($key);
    }

    public function set($key, $value, $ttl = null)
    {
        return $this->driver->set($key, $value, $ttl);
    }

    public function get($key)
    {
        return $this->driver->get($key);
    }

    public function delete($key)
    {
        return $this->driver->delete($key);
    }

    public function clear()
    {
        return $this->driver->clear();
    }

} 