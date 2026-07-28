<?php

namespace App\Domain\Common\Cache;

interface CacheClientInterface
{
    public function hSet(string $key, array $data): void;
    public function hGetAll(string $key): array;
    public function rPush(string $key, string $value): void;
    public function lPop(string $key): ?string;
    public function lPush(string $key, string $value): void;
    public function del(string $key): void;
    public function expire(string $key, int $seconds): void;
    public function multi(int $mode = 0): self;
    public function exec(): array;
}
