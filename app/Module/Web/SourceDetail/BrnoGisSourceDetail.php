<?php

namespace MP\Module\SourceDetail;

use Nette\Utils\Json;

/**
 * Specificka data pro detail pro data z GIS Brno
 */
class BrnoGisSourceDetail extends StandardPictogramsSourceDetail
{
    const DETAIL_DESCRIPTION_URL_MASK = 'http://gis.brno.cz/mapa/assets/local/documents/bezbarierove_objekty/Zdravotnicka_zarizeni_HTML/%s.html';

    /**
     * @param array $object
     *
     * @return array
     */
    public function prepareSourceData($object)
    {
        $ret = parent::prepareSourceData($object);
        $externalData = Json::decode($object['external_data'], true);

        if (!empty($externalData['ID_ZZ'])) {
            $ret['detail_description_url'] = sprintf(self::DETAIL_DESCRIPTION_URL_MASK, $externalData['ID_ZZ']);
        }

        return $ret;
    }
}
