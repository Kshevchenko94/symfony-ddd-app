<?php

namespace App\Infrastructure\Persistence\Postgres;

use PDO;
use Throwable;

final readonly class PostgresBulkLoader
{
    public function __construct(
        private PDO $pdo,
    )
    {
    }

    /**
     * @param list<string> $columns
     */
    public function loadFromCsv(
        string $csvFilePath,
        string $tempTableName,
        string $targetTableName,
        array $columns,
    ): void {
        $this->pdo->beginTransaction();
        try {
            $columnDefs = implode(', ', array_map(fn(string $col) => "$col TEXT", $columns));
            $this->pdo->exec("CREATE TEMP TABLE $tempTableName ($columnDefs) ON COMMIT DROP");

            $copySql = sprintf(
                "COPY %s FROM '%s' WITH (FORMAT csv, DELIMITER ',')",
                $tempTableName,
                $csvFilePath
            );
            $this->pdo->exec($copySql);

            $columnList = implode(', ', $columns);
            $this->pdo->exec("
                INSERT INTO $targetTableName ($columnList)
                SELECT $columnList FROM $tempTableName
                ON CONFLICT (id) DO NOTHING
            ");

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
