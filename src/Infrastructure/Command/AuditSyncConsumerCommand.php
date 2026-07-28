<?php

namespace App\Infrastructure\Command;

use App\Domain\Audit\Contract\AuditLogWriterInterface;
use App\Domain\Audit\Contract\OutboxQueueInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:audit:sync-to-postgres',
    description: 'Синхронизация аудит-логов из Redis в PostgreSQL через CSV'
)]
final class AuditSyncConsumerCommand extends Command
{
    private const int BATCH_SIZE = 500;

    public function __construct(
        private readonly OutboxQueueInterface $queue,
        private readonly AuditLogWriterInterface $writer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Начало синхронизации...');

        $totalProcessed = 0;

        while (true) {
            // 1. Забираем пачку
            $batchIds = $this->queue->popBatch(self::BATCH_SIZE);
            if (empty($batchIds)) {
                break;
            }

            try {
                // 2. Получаем данные и пишем в БД
                $this->writer->writeBatch($this->queue->fetchRecords($batchIds));
                // 3. Очищаем очередь
                $this->queue->clearBatch($batchIds);
                $totalProcessed += count($batchIds);
                $message = sprintf('Загружено %d записей', count($batchIds));
                $io->success($message);

            } catch (Throwable $e) {
                $io->error('Ошибка записи в БД: ' . $e->getMessage());

                // 4. Откат: возвращаем в очередь
                $this->queue->returnToQueue($batchIds);

                return Command::FAILURE;
            }
        }

        $io->success("Синхронизация завершена. Всего обработано: $totalProcessed записей.");
        return Command::SUCCESS;
    }
}
