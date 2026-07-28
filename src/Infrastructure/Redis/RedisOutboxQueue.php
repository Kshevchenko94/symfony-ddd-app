<?php

namespace App\Infrastructure\Redis;

use App\Domain\Audit\Contract\OutboxQueueInterface;
use App\Domain\Common\Cache\CacheClientInterface;
use Redis;

final readonly class RedisOutboxQueue implements OutboxQueueInterface
{
    private const string REDIS_QUEUE = 'outbox:audit_sync';

    public function __construct(
        private CacheClientInterface $cache,
    ) {
    }

    public function popBatch(int $size): array
    {
        $ids = [];
        for ($i = 0; $i < $size; $i++) {
            $id = $this->cache->lPop(self::REDIS_QUEUE);
            if ($id === null) {
                break;
            }
            $ids[] = $id;
        }
        return $ids;
    }

    public function fetchRecords(array $ids): iterable
    {
        if (empty($ids)) {
            return [];
        }

        $pipeline = $this->cache->multi(Redis::PIPELINE);
        foreach ($ids as $id) {
            $pipeline->hGetAll("audit_log:$id");
        }
        $results = $pipeline->exec();

        foreach ($results as $record) {
            if (!empty($record) && isset($record['id'])) {
                yield [
                    'id' => $record['id'],
                    'user_id' => $record['user_id'] ?? '',
                    'payload' => $record['payload'] ?? '',
                    'created_at' => (int)($record['created_at'] ?? 0),
                ];
            }
        }
    }

    public function clearBatch(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $pipeline = $this->cache->multi(Redis::PIPELINE);
        foreach ($ids as $id) {
            $pipeline->del("audit_log:$id");
        }
        $pipeline->exec();
    }

    public function returnToQueue(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $pipeline = $this->cache->multi(Redis::PIPELINE);
        // array_reverse критически важен для сохранения порядка FIFO
        foreach (array_reverse($ids) as $id) {
            $pipeline->lPush(self::REDIS_QUEUE, $id);
        }
        $pipeline->exec();
    }
}
