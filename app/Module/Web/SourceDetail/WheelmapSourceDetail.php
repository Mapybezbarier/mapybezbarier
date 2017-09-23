<?php

namespace MP\Module\SourceDetail;

use MP\Object\ObjectMetadata;
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
        $ret = [];

        $externalData = Json::decode($object['external_data'], true);

        $toiletBoolAsString = Arrays::get($externalData, 'wheelchair_toilet', null);
        $ret = array_merge($ret, $this->getWcAccessibility($toiletBoolAsString));

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

    /**
     * Pristupnost WC - dle wheelchair_toilet
     * 2 ruzne parametry pro 2 mirne odlisne vypisy
     *
     * @param string $boolAsSting
     *
     * @return array
     */
    protected function getWcAccessibility($boolAsSting)
    {
        $ret = [
            'wc_accessibility' => false,
            'wheelchair_toilet' => null,
        ];

        if ($boolAsSting) {
            if ('yes' === $boolAsSting) {
                $ret = [
                    'wc_accessibility' => [
                        'id' => 1,
                        'title' => ObjectMetadata::WC_ACCESSIBILITY_OK,
                    ],
                    'wheelchair_toilet' => true,
                ];
            } else if ('no' === $boolAsSting) {
                $ret = [
                    'wc_accessibility' => [
                        'id' => 3,
                        'title' => ObjectMetadata::WC_ACCESSIBILITY_NO,
                    ],
                    'wheelchair_toilet' => false,
                ];
            }
        }

        return $ret;
    }
}
