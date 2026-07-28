<?php

namespace App\Infrastructure\Listener;

use App\Domain\Common\Cache\CacheClientInterface;
use App\Domain\Common\Event\DomainEventInterface;
use Redis;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Uid\Uuid;

#[AsEventListener(event: DomainEventInterface::class)]
readonly class OutboxListener
{
    const INT ONE_DAY = 86400;

    public function __construct(
        private CacheClientInterface $cache,
    )
    {
    }

    public function __invoke(DomainEventInterface $event): void
    {
        $logId = Uuid::v4()->toString();
        $timestamp = time();
        $eventData = $event->toArray();

        $auditLogData = [
            'id' => $logId,
            'user_id' => $eventData['user_id'] ?? null,
            'payload' => json_encode($eventData, JSON_THROW_ON_ERROR),
            'created_at' => $timestamp
        ];

        // Redis транзакция для атомарности записи в память
        $this->cache->multi(Redis::PIPELINE);
        // Пишем тело лога
        $this->cache->hSet("audit_log:$logId", $auditLogData);
        // Храним в Redis 7 дней (этого с головой хватит воркеру, чтобы перенести запись)
        $this->cache->expire("audit_log:$logId", self::ONE_DAY);

        // Пушим ID в очередь на перенос
        $this->cache->rPush('outbox:audit_sync', $logId);

        $this->cache->exec();
    }
}
