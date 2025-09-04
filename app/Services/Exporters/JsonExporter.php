<?php

namespace App\Services\Exporters;

class JsonExporter extends Exporter
{
    public function export($data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}