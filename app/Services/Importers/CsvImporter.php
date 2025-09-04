<?php

namespace App\Services\Importers;

class CsvImporter extends Importer
{
    public function import($file): array
    {
        $data = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }
}