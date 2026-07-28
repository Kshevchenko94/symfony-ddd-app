<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Audit\Contract\AuditLogWriterInterface;
use App\Infrastructure\File\CsvFileGenerator;
use App\Infrastructure\Persistence\Postgres\PostgresBulkLoader;

final readonly class PostgresCsvAuditLogWriter implements AuditLogWriterInterface
{
    /** @var array|string[] */
    private const array HEADERS = ['id', 'user_id', 'payload', 'created_at'];
    private const string TEMP_TABLE = 'temp_audit_sync';
    private const string TARGET_TABLE = 'audit_log';

    public function __construct(
        private CsvFileGenerator $csvGenerator,
        private PostgresBulkLoader $bulkLoader,
    ) {
    }

    public function writeBatch(iterable $records): void
    {
        $csvFilePath = null;
        try {
            $csvFilePath = $this->csvGenerator->generate($records, self::HEADERS);

            $this->bulkLoader->loadFromCsv(
                $csvFilePath,
                self::TEMP_TABLE,
                self::TARGET_TABLE,
                self::HEADERS,
            );
        } finally {
            if ($csvFilePath !== null && file_exists($csvFilePath)) {
                unlink($csvFilePath);
            }
        }
    }
}
