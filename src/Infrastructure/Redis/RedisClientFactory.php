<?php

namespace App\Infrastructure\Redis;

use Redis;
use RuntimeException;

final readonly class RedisClientFactory
{
    public function __construct(
        private string $host,
        private string $port,
        private ?string $password = null,
        private int $database = 0,
        private float $timeout = 3.0,
    )
    {
    }

    public function create(): Redis
    {
        $redis = new Redis();

        $connected = $redis->pconnect($this->host, $this->port, $this->timeout);

        if (!$connected) {
            throw new RuntimeException(
                sprintf('Failed to connect to Redis at %s:%d', $this->host, $this->port)
            );
        }

        if ($this->password !== null && $this->password !== '') {
            if (!$redis->auth($this->password)) {
                throw new RuntimeException('Redis authentication failed');
            }
        }

        if (!$redis->select($this->database)) {
            throw new RuntimeException(sprintf('Failed to select Redis database %d', $this->database));
        }

        // Важные настройки для надёжности
        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
        $redis->setOption(Redis::OPT_READ_TIMEOUT, -1); // Бесконечное ожидание для очередей

        return $redis;
    }
}
