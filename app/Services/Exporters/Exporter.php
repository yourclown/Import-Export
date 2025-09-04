<?php

namespace App\Services\Exporters;

abstract class Exporter
{
    abstract public function export($data): string;
}