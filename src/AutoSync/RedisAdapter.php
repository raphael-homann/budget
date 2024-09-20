<?php

namespace App\AutoSync;

use Redis;

class RedisAdapter
{
    public static function createConnection(string $host, string $port, int $dbIndex=1): Redis
    {
        $redis = new Redis();
        $redis->connect($host, $port);
        $redis->select($dbIndex);
        return $redis;
    }

}
