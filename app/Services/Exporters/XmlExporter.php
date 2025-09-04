<?php

namespace App\Services\Exporters;

use SimpleXMLElement;

class XmlExporter extends Exporter
{
    public function export($data): string
    {
        $xml = new SimpleXMLElement('<root/>');
        foreach ($data as $row) {
            $item = $xml->addChild('item');
            foreach ($row as $key => $value) {
                $item->addChild($key, $value);
            }
        }
        return $xml->asXML();
    }
}