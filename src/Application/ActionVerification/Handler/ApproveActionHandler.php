<?php

namespace App\Application\ActionVerification\Handler;

use App\Application\ActionVerification\Command\ApproveActionCommand;
use App\Domain\ActionVerification\Repository\CriticalActionRepositoryInterface;
use DomainException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
readonly class ApproveActionHandler
{
    public function __construct(
        private CriticalActionRepositoryInterface $repository,
        private MessageBusInterface $messageBus
    )
    {
    }
    public function __invoke(ApproveActionCommand $command): void
    {
        $action = $this->repository->findById($command->actionId);

        if (null === $action) {
            throw new DomainException("Действие не найдено");
        }

        $action->approve();
        $this->repository->save($action);

        foreach ($action->pullDomainEvents() as $event) {
            $this->messageBus->dispatch($event);
        }
    }
}
