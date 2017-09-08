<?php

namespace MP\Module\SourceDetail;

use Nette\Utils\Arrays;
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

        // pokud neni nastaven nazev, tak pouzij nazev typu
        $nodeType = Arrays::get($externalData, 'node_type', null);

        if (!empty($nodeType)) {
            if (empty($object['title'])) {
                $ret['alternative_title'] = $nodeType;
            } else {
                $ret['node_type'] = $nodeType;
            }
        }

        return $ret;
    }
}
