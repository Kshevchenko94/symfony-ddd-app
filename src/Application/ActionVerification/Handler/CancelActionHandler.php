<?php

namespace App\Application\ActionVerification\Handler;

use App\Application\ActionVerification\Command\CancelActionCommand;
use App\Domain\ActionVerification\Repository\CriticalActionRepositoryInterface;
use DomainException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class CancelActionHandler
{
    public function __construct(
        private CriticalActionRepositoryInterface $repository,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function __invoke(CancelActionCommand $command): void
    {
        $action = $this->repository->findById($command->actionId);

        if (null === $action) {
            throw new DomainException("Действие не найдено");
        }

        $action->cancel();
        $this->repository->save($action);

        foreach ($action->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
