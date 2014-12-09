<?php
namespace Stockpile;

class CacheItem implements CacheItemInterface
{

    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var \DateTime
     */
    private $expiration;

    private $hit = false;

    public function __construct($key, $value = null, $ttl = null, $hit = false)
    {
        $this->setKey($key)->set($value)->setExpiration($ttl)->setHit($hit);
    }

    protected function setKey($key)
    {
        $key = trim(strval($key));

        if (empty($key)) throw new \InvalidArgumentException();

        $this->key = $key;

        return $this;
    }

    protected function setHit($hit)
    {
        $this->hit = $hit === true;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function get()
    {
        return $this->value;
    }

    public function set($value = null)
    {
        $this->value = $value;

        return $this;
    }

    public function isHit()
    {
        return $this->hit;
    }

    final public function isMiss()
    {
        return !$this->isHit();
    }

    public function exists()
    {

    }

    public function isRegenerating()
    {

    }

    public function setExpiration($ttl = null)
    {
        $this->expiration = Driver::normalizeTtl($ttl);

        return $this;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

} 