<?php

namespace MP\Module\SourceDetail;

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
            'accessibility' => $externalData['attr1']['title'],
            'wc_accessible_bool' => ('yes' === $externalData['attr2']),
            'parking_accessible_bool' => ('yes' === $externalData['attr3']),
        ];
    }
}
