<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\ActionVerification\Entity\CriticalAction;
use App\Domain\ActionVerification\Repository\CriticalActionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CriticalAction>
 */
class CriticalActionRepository extends ServiceEntityRepository implements CriticalActionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CriticalAction::class);
    }

    public function save(CriticalAction $action): void
    {
        $this->getEntityManager()->persist($action);
        $this->getEntityManager()->flush();
    }

    public function findById(string $id): ?CriticalAction
    {
        return $this->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findByUserId(string $userId): array
    {
        return $this->findBy(['userId' => $userId]);
    }
}
