<?php

namespace MP\Module\SourceDetail;

use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use Nette\Utils\Json;

/**
 * Specificka data pro detail pro data z CB
 */
class CBSourceDetail implements ISourceDetail
{
    /** @var array mapa ciselnikoveho atributu - pristupnost */
    protected $standardPictogramsMap = [
        13 => 'parking',
        0 => 'difficult_surface',
        1 => 'difficult_inclination',
        // 2 = přístupný hlavním vchodem
        // 3 = přístupný vedlejším vchodem
        4 => 'stairs',
        5 => 'spiral_stairs',
        6 => 'elevator',
        7 => 'platform',
        8 => 'rampskids',
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
        $externalData = Json::decode($object['external_data'], true);
        $standardPictograms = Arrays::get($externalData, 'standard_pictograms', []);

        return [
            'wc_accessibility' => $this->getWcAccessibility($standardPictograms),
            'entrance1_accessibility' => Arrays::get($standardPictograms, 2, null),
            'entrance2_accessibility' => Arrays::get($standardPictograms, 3, null),
            'pictograms' => $this->getStandardPictogramsData($standardPictograms),
            'image' => null,
        ];
    }

    /**
     * Urceni, ktere standardni piktogramy se maji zobrazit
     * vsechny piktogramy vraci tvar: [
     *     'value' => string|boolean
     *     'description' => string|null // nepovinne
     *     'accessible' => boolean // true - zelene, false - cervene, null - bez zvyrazneni
     * ], klic pak urcuje ikonku a hlavni text
     *
     * @param array $standardPictograms
     *
     * @return array
     */
    protected function getStandardPictogramsData($standardPictograms)
    {
        foreach ($this->standardPictogramsMap as $dbKey => $templateKey) {
            $ret[$templateKey] = [
                'value' => Arrays::get($standardPictograms, $dbKey, null),
                'accessible' => null,
            ];
        }

        $ret = $this->getWcPictograms($standardPictograms) + $ret;

        return $ret;
    }

    /**
     * Pripravi piktogramy WC pro vsechny 3 pripadne pristupnosti
     *
     * @param array $standardPictograms
     *
     * @return array
     */
    protected function getWcPictograms($standardPictograms)
    {
        $ret = [];

        if ($standardPictograms[10]) {
            $ret['wc_1'] = [
                'key' => ObjectMetadata::WC_ACCESSIBILITY_OK,
                'value' => '',
                'accessible' => null,
            ];
        }

        if ($standardPictograms[11]) {
            $ret['wc_2'] = [
                'key' => ObjectMetadata::WC_ACCESSIBILITY_PARTLY,
                'value' => '',
                'accessible' => null,
            ];
        }

        if ($standardPictograms[12]) {
            $ret['wc_3'] = [
                'key' => ObjectMetadata::WC_ACCESSIBILITY_NO,
                'value' => '',
                'accessible' => null,
            ];
        }

        return $ret;
    }

    /**
     * Pristupnost WC - nejprisutpnejsi kategorie u wc v celem objektu
     *
     * @param array $standardPictograms
     *
     * @return null|array
     */
    protected function getWcAccessibility($standardPictograms)
    {
        $ret = false;

        if ($standardPictograms[12]) {
            $ret = [
                'id' => 3,
                'title' => ObjectMetadata::WC_ACCESSIBILITY_NO,
            ];
        }

        if ($standardPictograms[11]) {
            $ret = [
                'id' => 2,
                'title' => ObjectMetadata::WC_ACCESSIBILITY_PARTLY,
            ];
        }

        if ($standardPictograms[10]) {
            $ret = [
                'id' => 1,
                'title' => ObjectMetadata::WC_ACCESSIBILITY_OK,
            ];
        }

        return $ret;
    }
}
