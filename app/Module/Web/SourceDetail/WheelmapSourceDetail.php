<?php

namespace MP\Module\SourceDetail;

use Nette\Utils\Json;

/**
 * Specificka data pro detail pro data z wheelmap.org
 */
class WheelmapSourceDetail implements ISourceDetail
{
    /**
     * @param array $object
     *
     * @return array
     */
    public function prepareSourceData($object)
    {
        $ret = [
            'wheelchair_toilet' => null,
        ];

        $externalData = Json::decode($object['external_data'], true);

        if (isset($externalData['wheelchair_toilet'])) {
            if ('yes' == $externalData['wheelchair_toilet']) {
                $ret = [
                    'wheelchair_toilet' => true,
                ];
            } else if ('no' == $externalData['wheelchair_toilet']) {
                $ret = [
                    'wheelchair_toilet' => false,
                ];
            }
        }

        return $ret;
    }
}
