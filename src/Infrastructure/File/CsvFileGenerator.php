<?php

namespace App\Infrastructure\File;

use RuntimeException;

final readonly class CsvFileGenerator
{
    /**
     * @param iterable<array<string, mixed>> $records
     * @param list<string> $headers
     * @return string Путь к созданному файлу
     */
    public function generate(iterable $records, array $headers): string
    {
        $filePath = sys_get_temp_dir() . '/bulk_import_' . uniqid('', true) . '.csv';
        $handle = fopen($filePath, 'w');

        if ($handle === false) {
            throw new RuntimeException("Не удалось создать временный файл: $filePath");
        }

        try {
            fputcsv($handle, $headers);
            foreach ($records as $record) {
                fputcsv($handle, $record);
            }
        } finally {
            fclose($handle);
        }

        return $filePath;
    }
}
