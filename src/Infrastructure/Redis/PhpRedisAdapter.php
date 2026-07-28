<?php

namespace App\Infrastructure\Redis;

use App\Domain\Common\Cache\CacheClientInterface;
use Redis;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class PhpRedisAdapter implements CacheClientInterface
{
    private ?Redis $pipeline;

    public function __construct(
        #[Autowire(service: 'app.redis.client')]
        private readonly Redis $redis,
    )
    {
        $this->pipeline = null;
    }

    public function hSet(string $key, array $data): void
    {
        $this->redis->hSet($key, $data);
    }

    public function hGetAll(string $key): array
    {
        return $this->redis->hGetAll($key) ?: [];
    }

    public function rPush(string $key, string $value): void
    {
        $this->redis->rPush($key, $value);
    }

    public function lPop(string $key): ?string
    {
        $result = $this->redis->lPop($key);
        return $result === false ? null : $result;
    }

    public function lPush(string $key, string $value): void
    {
        $this->redis->lPush($key, $value);
    }

    public function del(string $key): void
    {
        $this->redis->del($key);
    }

    public function expire(string $key, int $seconds): void
    {
        $this->redis->expire($key, $seconds);
    }

    public function multi(int $mode = 0): self
    {
        $clone = clone $this;
        $clone->pipeline = $this->redis->multi($mode);
        return $clone;
    }

    public function exec(): array
    {
        if ($this->pipeline === null) {
            throw new RuntimeException('Pipeline not started. Call multi() first.');
        }
        return $this->pipeline->exec() ?: [];
    }
}
