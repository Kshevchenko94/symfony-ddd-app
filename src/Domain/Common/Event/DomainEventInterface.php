<?php

namespace App\Domain\Common\Event;

interface DomainEventInterface
{
    public function getRoutingKey(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
