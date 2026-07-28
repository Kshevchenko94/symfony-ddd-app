<?php

namespace App\Infrastructure\Persistence\Pdo;

use Doctrine\DBAL\Connection;
use PDO;
use RuntimeException;

final readonly class DoctrinePdoProvider implements PdoProviderInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getNativePdo(): PDO
    {
        $native = $this->connection->getNativeConnection();

        // DBAL 3.x может вернуть сразу PDO
        if ($native instanceof PDO) {
            return $native;
        }

        // DBAL 4.x возвращает объект-обёртку. Проверяем, что это объект и у него есть нужный метод.
        if (is_object($native) && method_exists($native, 'getNativeConnection')) {
            $pdo = $native->getNativeConnection();
            if ($pdo instanceof PDO) {
                return $pdo;
            }
        }

        throw new RuntimeException('Не удалось получить нативное соединение PDO');
    }
}
