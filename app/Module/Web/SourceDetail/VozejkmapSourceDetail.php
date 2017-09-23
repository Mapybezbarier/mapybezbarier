<?php

namespace MP\Module\SourceDetail;

use MP\Object\ObjectMetadata;
use Nette\Utils\Json;

/**
 * Specificka data pro detail pro data z vozejkmap.cz
 */
class VozejkmapSourceDetail implements ISourceDetail
{
    /**
     * @param array $object
     *
     * @return array
     */
    public function prepareSourceData($object)
    {
        $externalData = Json::decode($object['external_data'], true);

        return [
            'custom_category' => (isset($externalData['location_type']) ? $externalData['location_type']['title'] : null),
            'vozejkmapAccessibility' => $externalData['attr1']['title'],
            'wc_accessible_bool' => ('yes' === $externalData['attr2']),
            'parking_accessible_bool' => ('yes' === $externalData['attr3']),
            'wc_accessibility' => $this->getWcAccessibility($externalData['attr2']),
        ];
    }

    /**
     * Pristupnost WC - dle attr2
     *
     * @param string $boolAsSting
     *
     * @return bool|array
     */
    protected function getWcAccessibility($boolAsSting)
    {
        $ret = false;

        if ($boolAsSting) {
            if ('yes' === $boolAsSting) {
                $ret = [
                    'id' => 1,
                    'title' => ObjectMetadata::WC_ACCESSIBILITY_OK,
                ];
            } else if ('no' === $boolAsSting) {
                $ret = [
                    'id' => 3,
                    'title' => ObjectMetadata::WC_ACCESSIBILITY_NO,
                ];
            }
        }

        return $ret;
    }
}
