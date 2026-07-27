<?php

namespace App\Domain\Common\Event;

interface DomainEventInterface
{
    public function getRoutingKey(): string;

    public function toArray(): array;
}
