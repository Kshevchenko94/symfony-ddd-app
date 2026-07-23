<?php

namespace App\Domain\ActionVerification\Repository;

use App\Domain\ActionVerification\Entity\CriticalAction;

interface CriticalActionRepositoryInterface
{
    public function save(CriticalAction $action): void;
    public function findById(string $id): ?CriticalAction;

    public function findByUserId(string $userId): array;
}
