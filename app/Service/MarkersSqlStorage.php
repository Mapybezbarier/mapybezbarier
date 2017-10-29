<?php

namespace MP\Service;

use MP\Mapper\MarkersCacheMapper;
use Nette\Caching\IStorage;

/**
 * Jednoucelova cache storage pro ukladani predpocitaneho JSONu o markerech
 * Uloziste je DB tabulka, invalidace se provadi prostrednictvim DB triggeru
 */
class MarkersSqlStorage implements IStorage
{
    /** @var MarkersCacheMapper */
    protected $mapper;
    /**
     * @param MarkersCacheMapper $mapper
     */
    public function __construct(MarkersCacheMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Read from cache.
     * @param string $key
     * @return string|null JSON data
     */
    public function read($key)
    {
        $ret = $this->mapper->read($key, time());

        if (!$ret) {
            $ret = null;
        }

        return $ret;
    }

    /**
     * Prevents item reading and writing. Lock is released by write() or remove().
     * @param  string
     * @return void
     */
    public function lock($key)
    {
    }

    /**
     * Writes item into the cache.
     * @param  string
     * @param  mixed
     * @return void
     */
    public function write($key, $data, array $dependencies)
    {
        $this->mapper->write($key, $data, time());
    }

    /**
     * Removes item from the cache.
     * @param  string
     * @return void
     */
    public function remove($key)
    {
        $this->mapper->remove($key);
    }


    /**
     * Removes items from the cache by conditions & garbage collector.
     * @param  array  conditions
     * @return void
     */
    public function clean(array $conditions)
    {
        $this->mapper->remove();
    }
}