<?php

namespace App\Traits;

use App\Services\Importers\Importer;

trait Importable
{
    public static function importData(string $filePath, string $format): void
    {
        $importer = self::getImporter($format);
        $data = $importer->import($filePath);
        foreach ($data as $row) {
            self::create($row); // Assumes data matches model attributes
        }
    }

    protected static function getImporter(string $format): Importer
    {
        return match ($format) {
            'csv' => new \App\Services\Importers\CsvImporter(),
            'json' => new \App\Services\Importers\JsonImporter(),
            default => throw new \Exception('Unsupported format'),
        };
    }
}