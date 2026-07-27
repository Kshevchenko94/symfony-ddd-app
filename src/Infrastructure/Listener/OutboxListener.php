<?php

namespace App\Infrastructure\Listener;

use App\Domain\Common\Event\DomainEventInterface;
use Redis;
use Symfony\Component\Uid\Uuid;

readonly class OutboxListener
{
    const INT ONE_DAY = 86400;

    public function __construct(
        private Redis $redis,
    )
    {
    }

    public function __invoke(DomainEventInterface $event): void
    {
        $logId = Uuid::v4()->toString();
        $timestamp = time();

        $auditLogData = [
            'id' => $logId,
            'user_id' => $event->toArray()['user_id'],
            'payload' => json_encode($event->toArray()),
            'created_at' => $timestamp
        ];

        // Redis транзакция для атомарности записи в память
        $this->redis->multi();
        // Пишем тело лога
        $this->redis->hMSet("audit_log:$logId", $auditLogData);
        // Храним в Redis 7 дней (этого с головой хватит воркеру, чтобы перенести запись)
        $this->redis->expire("audit_log:$logId", self::ONE_DAY);

        // Пушим ID в очередь на перенос
        $this->redis->rPush('outbox:audit_sync', $logId);

        $this->redis->exec();
    }
}
