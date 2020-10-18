<?php
namespace App;

class RateLimiter
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $throttleKey
     * @param int $limit
     * @param int $seconds
     * @return bool
     */
    public function Throttle(string $throttleKey,int $limit,int $seconds) : bool
    {
        // Current time in seconds since Unix Epoch
        $now = time();

        //Removes all elements in the sorted set stored at key with a score between min and max.
        //The old element must be removed to allow new requests.
        $minScore = $now - $seconds;
        $this->storage->remove($throttleKey,0,$minScore);

        //Count the number of requests. If it exceeds the limit, we don’t allow the action.
        $requestCount = $this->storage->count($throttleKey);
        if( $requestCount >= $limit){
            return false;
        }

        //Add the current timestamp to the set.
        $this->storage->add($throttleKey,$now);

        //Set a TTL equal to the rate limiting interval on the set.
        //Because if we do not have any request within the rate limiting interval, we must delete the set (to save space).
        $this->storage->expire($throttleKey,$seconds);

        return true;

    }

}