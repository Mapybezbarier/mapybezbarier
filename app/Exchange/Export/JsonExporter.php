<?php

namespace MP\Exchange\Export;
use Nette\Utils\Json;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class JsonExporter implements IExpoter
{
    /**
     * @param array $data
     * @return string
     */
    public function export(array $data)
    {
        try {
            $objects = Json::encode($data);
        } catch (\Nette\Utils\JsonException $e) {
            throw new \MP\Exchange\Exception\ExportException($e->getMessage());
        }

        return $objects;
    }
}
