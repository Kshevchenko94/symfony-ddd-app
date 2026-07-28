<?php

namespace App\Domain\Audit\Contract;

interface OutboxQueueInterface
{
    /**
     * Забирает пачку ID из очереди.
     * @return list<string>
     */
    public function popBatch(int $size): array;

    /**
     * Получает данные по ID. Возвращает Generator для экономии памяти.
     * @param list<string> $ids
     * @return iterable<array{id: string, user_id: string, payload: string, created_at: int}>
     */
    public function fetchRecords(array $ids): iterable;

    /**
     * Удаляет обработанные записи из Redis.
     * @param list<string> $ids
     */
    public function clearBatch(array $ids): void;

    /**
     * Возвращает записи в начало очереди при ошибке (сохраняя FIFO).
     * @param list<string> $ids
     */
    public function returnToQueue(array $ids): void;
}
