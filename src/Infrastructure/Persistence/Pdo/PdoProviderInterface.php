<?php

namespace App\Infrastructure\Persistence\Pdo;

use PDO;

interface PdoProviderInterface
{
    public function getNativePdo(): PDO;
}
