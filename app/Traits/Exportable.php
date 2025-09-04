<?php

namespace App\Traits;

use App\Services\Exporters\Exporter;

trait Exportable
{
    public static function exportData(string $format): string
    {
        $data = self::all()->toArray(); // Example: export all records
        $exporter = self::getExporter($format);
        return $exporter->export($data);
    }

    protected static function getExporter(string $format): Exporter
    {
        return match ($format) {
            'csv' => new \App\Services\Exporters\CsvExporter(),
            'json' => new \App\Services\Exporters\JsonExporter(),
            'xml' => new \App\Services\Exporters\XmlExporter(),
            default => throw new \Exception('Unsupported format'),
        };
    }
}