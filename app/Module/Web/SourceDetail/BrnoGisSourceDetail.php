<?php

namespace MP\Module\SourceDetail;

use Nette\Utils\Json;

/**
 * Specificka data pro detail pro data z GIS Brno
 */
class BrnoGisSourceDetail extends StandardPictogramsSourceDetail
{
    const DETAIL_DESCRIPTION_URL_MASK = 'https://gis.brno.cz/ost/mapa-pristupnosti-export/export.php?lang=cs&ogc_fid=%s';

    /**
     * @var array mapa ciselnikoveho atributu - pristupnost
     * v podkladech GIS Brno je prohozeno poradi vytah x plosina x rampa
     */
    protected $standardPictogramsMap = [
        13 => 'parking',
        0 => 'difficult_surface',
        1 => 'difficult_inclination',
        // 2 = přístupný hlavním vchodem
        // 3 = přístupný vedlejším vchodem
        4 => 'stairs',
        5 => 'spiral_stairs',
        7 => 'elevator',
        8 => 'platform',
        6 => 'rampskids',
        9 => 'narrowed_passage',
        // 10 = WC I.
        // 11 = WC II.
        // 12 = WC NOK
    ];

    /**
     * @param array $object
     *
     * @return array
     */
    public function prepareSourceData($object)
    {
        $ret = parent::prepareSourceData($object);
        $externalData = Json::decode($object['external_data'], true);

        if (!empty($externalData['ID_ZZ']) && !empty($externalData['cislo_zdravotnickeho_zarizeni'])) {
            $ret['detail_description_url'] = sprintf(self::DETAIL_DESCRIPTION_URL_MASK, $externalData['ID_ZZ']);
        }

        return $ret;
    }
}
