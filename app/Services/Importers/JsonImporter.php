<?php

namespace App\Services\Importers;

class JsonImporter extends Importer
{
    public function import($file): array
    {
        $content = file_get_contents($file->getPathname());
        return json_decode($content, true);
    }
}