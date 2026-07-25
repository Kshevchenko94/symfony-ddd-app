<?php

namespace App\Infrastructure\Messaging\Handler;

use App\Domain\ActionVerification\Event\ActionApprovedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler] // Атрибут говорит Symfony: "Этот класс умеет обрабатывать сообщения"
readonly class ApprovedActionHandler
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {
    }

    public function __invoke(ActionApprovedEvent $event): void
    {
        $this->logger->info('🎉 Получено событие ActionVerifiedEvent из RabbitMQ!', [
            'action_id' => $event->id,
            'user_id' => $event->userId,
            'processed_at' => $event->createdAt->format('Y-m-d H:i:s'),
        ]);
    }
}
