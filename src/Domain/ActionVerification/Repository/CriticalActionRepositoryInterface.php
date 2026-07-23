<?php

namespace App\Domain\ActionVerification\Repository;

use App\Domain\ActionVerification\Entity\CriticalAction;

interface CriticalActionRepositoryInterface
{
    public function save(CriticalAction $action): void;
    public function findById(string $id): ?CriticalAction;

    /**
     * @param string $userId
     * @return array<CriticalAction>
     */
    public function findByUserId(string $userId): array;
}
