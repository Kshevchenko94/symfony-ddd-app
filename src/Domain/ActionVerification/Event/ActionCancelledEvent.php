<?php

namespace App\Domain\ActionVerification\Event;

use App\Domain\Common\Event\DomainEventInterface;
use DateTimeImmutable;
use DateTimeInterface;

class ActionCancelledEvent implements DomainEventInterface
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $reason,
        public DateTimeImmutable $createdAt,
    )
    {
    }

    public function getRoutingKey(): string
    {
        return 'action_verification.cancelled';
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'reason' => $this->reason,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
        ];
    }
}
