<?php

namespace App\Domain\Audit\Contract;

interface AuditLogWriterInterface
{
    /**
     * @param iterable<array{id: string, user_id: string, payload: string, created_at: int}> $records
     */
    public function writeBatch(iterable $records): void;
}
