<?php
// Autoload files using the Composer autoloader.
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $client = new \Predis\Client(['scheme' => 'tcp', 'host'   => 'test-redis', 'port'   => 6379]);
    $redis = new \App\Redis($client);
    $rateLimiter = new \App\RateLimiter($redis);


    $key = "user-one-identifier";
    $limit = 20;
    $seconds = 60;//equal to one minute

    // Limit user request ( 20 request per 1 minutes)
    $rateLimiter->Throttle($key,$limit,$seconds);

    
    $keys = $client->keys('*');
    if(count($keys)) {
        dump("Storage contains: ");
        foreach ($keys as $key)
        {
            dump('TTL ( Time left until the sorted-set removal ): '.$client->ttl($key));
            $members = $client->zrange($key,0,-1);
            dump([$key => $members]);

            $remainTime = (int)$members[0] - (time()-$seconds);
            dump(sprintf("Oldest request is %s, and remain time until removal %s seconds",$members[0],$remainTime));

            $remainTime = (int)$members[count($members)-1] - (time()-$seconds);
            dump(sprintf("Newest request is %s, and remain time until removal %s seconds",$members[count($members)-1],$remainTime));
        }
    }

} catch (\Throwable $throwable) {
    dump($throwable->getMessage());die();
}
