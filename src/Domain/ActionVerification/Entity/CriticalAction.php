<?php

namespace App\Domain\ActionVerification\Entity;

use App\Domain\ActionVerification\Event\ActionRejectedEvent;
use App\Domain\ActionVerification\Event\ActionVerifiedEvent;
use App\Domain\ActionVerification\ValueObject\ActionStatus;
use App\Domain\ActionVerification\ValueObject\ActionType;
use DateTimeImmutable;
use DomainException;

class CriticalAction
{
    private DateTimeImmutable $createdAt;
    private array $domainEvents = [];
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        private ActionType $type,
        private ActionStatus $status,
        private ?DateTimeImmutable $processedAt = null,
        private ?string $rejectionReason,
    )
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function __destruct()
    {
        $this->domainEvents = [];
    }

    public function approve(): void
    {
        if ($this->status === ActionStatus::REJECTED) {
            throw new DomainException("Нельзя одобрить отклоненное действие");
        }

        if ($this->status === ActionStatus::APPROVED) {
            throw new DomainException("Действие уже одобрено");
        }

        $this->status = ActionStatus::APPROVED;
        $this->processedAt = new DateTimeImmutable();
        $this->domainEvents[] = new ActionVerifiedEvent(
            $this->id,
            $this->userId,
            new DateTimeImmutable()
        );
    }
    public function reject(string $reason): void
    {
        if ($this->status !== ActionStatus::PENDING) {
            throw new DomainException("Можно отклонить только ожидающее действие");
        }

        $this->status = ActionStatus::REJECTED;
        $this->processedAt = new DateTimeImmutable();
        $this->rejectionReason = $reason;
        $this->domainEvents[] = new ActionRejectedEvent(
            $this->id,
            $this->userId,
            $reason,
            new DateTimeImmutable()
        );
    }

    public function getStatus(): ActionStatus
    {
        return $this->status;
    }

    public function getType(): ActionType
    {
        return $this->type;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
