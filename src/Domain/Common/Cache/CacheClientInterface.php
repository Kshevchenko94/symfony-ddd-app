<?php

namespace App\Domain\Common\Cache;

interface CacheClientInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function hSet(string $key, array $data): void;

    /**
     * @return array<string, mixed>
     */
    public function hGetAll(string $key): array;
    public function rPush(string $key, string $value): void;
    public function lPop(string $key): ?string;
    public function lPush(string $key, string $value): void;
    public function del(string $key): void;
    public function expire(string $key, int $seconds): void;
    public function multi(int $mode = 0): self;

    /**
     * @return array<mixed>
     */
    public function exec(): array;
}
