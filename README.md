# RateLimiter

A rate limiting can limit the rate of requests.

Redis has a data structure that we can use – the sorted set. Here’s the algorithm we came up with:

- Each user has a sorted set associated with them. The keys and values are identical, and equal to the (second) times when actions were attempted.

- When a user attempts to perform an action, we first drop all elements of the set which occured before one interval ago. This can be accomplished with Redis’s ZREMRANGEBYSCORE command. 

- Fetch all elements of the set, using ZRANGE(0, -1).

- Add the current timestamp to the set, using ZADD.

- Set a TTL equal to the rate-limiting interval on the set (to save space).

- After all operations are completed, count the number of fetched elements. If it exceeds the limit, don’t allow the action.


# Installation
1- Download Composer or update composer self-update.
```bash
docker-compose exec app composer install
```
2- You should now be able to visit the path to where you installed the app and see the default home page http://127.0.0.1:8000/.
