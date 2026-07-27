<?php

namespace App\Infrastructure\Listener;

use App\Domain\Common\Event\DomainEventInterface;
use Psr\Log\LoggerInterface;

readonly class AuditLogListener
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {
    }

    public function __invoke(DomainEventInterface $event): void
    {
        $this->logger->info("Критическое действие пользователя: " . $event->getRoutingKey(), [
            'gelf_format' => true, // маркер для фильтрации Logstash
            'audit_details' => $event->toArray()
        ]);
    }
}
