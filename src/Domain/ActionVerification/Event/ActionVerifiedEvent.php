<?php

namespace App\Domain\ActionVerification\Event;

use DateTimeImmutable;

readonly class ActionVerifiedEvent
{
    public function __construct(
        public string $id,
        public string $userId,
        public DateTimeImmutable $createdAt,
    ) {}
}
