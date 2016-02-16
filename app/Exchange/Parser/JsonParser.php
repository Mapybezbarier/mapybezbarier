<?php

namespace MP\Exchange\Parser;

use Nette\Utils\Json;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class JsonParser implements IParser
{
    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $parsedData = [];

        if (!empty($data)) {
            try {
                $parsedData = Json::decode($data, Json::FORCE_ARRAY);
            } catch (\Nette\Utils\JsonException $e) {
                throw new \MP\Exchange\Exception\ParseException($e->getMessage());
            }
        }

        return $parsedData;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return IParser::TYPE_INTERNAL;
    }
}
