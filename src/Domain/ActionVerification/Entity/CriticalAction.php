<?php

namespace App\Domain\ActionVerification\Entity;

use App\Domain\ActionVerification\Event\ActionCancelledEvent;
use App\Domain\ActionVerification\Event\ActionRejectedEvent;
use App\Domain\ActionVerification\Event\ActionApprovedEvent;
use App\Domain\ActionVerification\Repository\CriticalActionRepositoryInterface;
use App\Domain\ActionVerification\ValueObject\ActionStatus;
use App\Domain\ActionVerification\ValueObject\ActionType;
use App\Domain\Common\Event\DomainEventInterface;
use DateTimeImmutable;
use DomainException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CriticalActionRepositoryInterface::class)]
#[ORM\Table(name: 'critical_actions', schema: 'action_verification')]
class CriticalAction
{
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    /**
     * @var array<DomainEventInterface>
     */
    private array $domainEvents = [];
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        public readonly Uuid $id,
        #[ORM\Column(type: 'string', length: 36)]
        public readonly string $userId,
        #[ORM\Column(type: 'string', enumType: ActionType::class)]
        private ActionType $type,
        #[ORM\Column(type: 'string', enumType: ActionStatus::class)]
        private ActionStatus $status,
        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ?DateTimeImmutable $processedAt = null,
        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private ?string $rejectionReason = null,
    )
    {
        $this->createdAt = new DateTimeImmutable();
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
        $this->domainEvents[] = new ActionApprovedEvent(
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

    public function cancel(): void
    {
        if ($this->status !== ActionStatus::PENDING) {
            throw new DomainException("Можно отменить только ожидающее действие");
        }

        $this->status = ActionStatus::CANCELLED;
        $this->processedAt = new DateTimeImmutable();
        $this->rejectionReason = 'Cancelled by user';

        $this->domainEvents[] = new ActionCancelledEvent(
            $this->id,
            $this->userId,
            'Cancelled by user',
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getProcessedAt(): ?DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    /**
     * @return array<DomainEventInterface>
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
