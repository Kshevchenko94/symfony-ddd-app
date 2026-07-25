<?php

namespace App\Domain\ActionVerification\Event;

use App\Domain\Common\Event\DomainEventInterface;
use DateTimeImmutable;

readonly class ActionApprovedEvent implements DomainEventInterface
{
    public function __construct(
        public string $id,
        public string $userId,
        public DateTimeImmutable $createdAt,
    ) {}

    public function getRoutingKey(): string
    {
        return 'action_verification.approved';
    }
}
