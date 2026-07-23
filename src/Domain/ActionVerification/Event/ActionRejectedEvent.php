<?php

namespace App\Domain\ActionVerification\Event;

use DateTimeImmutable;

readonly class ActionRejectedEvent
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $reason,
        public DateTimeImmutable $createdAt,
    ) {}
}
