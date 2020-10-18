<?php

namespace App;
use Predis\Client;

class Redis implements StorageInterface{

    public $client;

    /**
     * Redis constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function add(string $key,string $value)
    {
        $this->client->zadd($key, [$value => $value]);
    }

    /**
     * @param string $key
     * @param int $seconds
     */
    public function expire(string $key,int $seconds)
    {
        $this->client->expire($key, $seconds);
    }

    /**
     * @param string $key
     * @return int
     */
    public function count(string $key) : int
    {
        return $this->client->zcard($key);
    }

    /**
     * @param string $key
     * @param int $min
     * @param int $max
     * @return int
     */
    public function remove(string $key,int $min,int $max) : int
    {
        return $this->client->zremrangebyscore($key,$min,$max);
    }

}