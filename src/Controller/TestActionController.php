<?php

namespace App\Controller;

use App\Application\ActionVerification\Command\ApproveActionCommand;
use App\Domain\ActionVerification\Entity\CriticalAction;
use App\Domain\ActionVerification\Repository\CriticalActionRepositoryInterface;
use App\Domain\ActionVerification\ValueObject\ActionStatus;
use App\Domain\ActionVerification\ValueObject\ActionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/test')]
class TestActionController extends AbstractController
{
    #[Route('/create-action', methods: ['POST'])]
    public function createAction(
        CriticalActionRepositoryInterface $repository,
        MessageBusInterface $bus
    ): JsonResponse {
        // Создаем тестовое действие
        $action = new CriticalAction(
            id: Uuid::v7(),
            userId: 'user_123',
            type: ActionType::MONEY_TRANSFER,
            status: ActionStatus::PENDING,
        );

        $repository->save($action);

        return new JsonResponse([
            'message' => 'Действие создано',
            'action_id' => $action->id,
        ]);
    }

    #[Route('/approve-action', methods: ['POST'])]
    public function approveAction(
        Request $request,
        MessageBusInterface $bus
    ): JsonResponse {
        $actionId = $request->query->get('id');

        if (!$actionId) {
            return new JsonResponse(['error' => 'Укажите action_id'], 400);
        }

        // Отправляем команду на одобрение
        $command = new ApproveActionCommand($actionId);
        $bus->dispatch($command);

        return new JsonResponse([
            'message' => 'Команда отправлена!',
            'action_id' => $actionId,
        ]);
    }
}
