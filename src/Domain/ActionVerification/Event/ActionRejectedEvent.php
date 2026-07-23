<?php

namespace App\Domain\ActionVerification\Event;

use App\Domain\Common\Event\DomainEventInterface;
use DateTimeImmutable;

readonly class ActionRejectedEvent implements DomainEventInterface
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $reason,
        public DateTimeImmutable $createdAt,
    ) {}
}
