<?php

namespace MP\Module\SourceDetail;

use MP\Object\ObjectMetadata;
use Nette\Utils\Json;

/**
 * Specificka data pro detail pro data z WC Kompas
 */
class WcKompasSourceDetail implements ISourceDetail
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
            'custom_pictograms' => $this->getCustomPictogramsData($object, $externalData),
            'wc_accessibility' => $this->getWcAccessibility($object['accessibility_id']),
        ];
    }

    /**
     * Urceni, ktere piktogramy se maji zobrazit a jejich textoveho upresneni
     * vsechny piktogramy vraci tvar: [
     *     'value' => string|boolean
     *     'description' => string|null // nepovinne
     *     'accessible' => boolean // true - zelene, false - cervene, null - bez zvyrazneni
     * ], klic pak urcuje ikonku a hlavni text
     * "Piktogramy" z WC Kompas vsak grafickou ikonku nemaji
     *
     * @param array $object
     * @param array $externalData
     *
     * @return array
     */
    protected function getCustomPictogramsData($object, $externalData)
    {
        $ret = [
            'euro_key_access' => $this->getPictogramWithEuroKeyAccess($externalData),
            'free' => $this->getPictogramFree($externalData),
            'accessible' => $this->getPictogramAccessible($externalData),
            'card_priority' => $this->getPictogramCardPriority($externalData),
            'card_free' => $this->getPictogramCardFree($externalData),
            'with_card_access' => $this->getPictogramWithCardAccess($externalData),
        ];

        return $ret;
    }

    /**
     * Je pristupny pouze s Euroklíčem
     * obracena logika oproti importovanemu atributu without_euro_key_access
     *
     * @param array $externalData
     *
     * @return null|array
     */
    protected function getPictogramWithEuroKeyAccess($externalData)
    {
        $ret = [
            'value' => null,
            'accessible' => null,
        ];

        if (isset($externalData['without_euro_key_access'])) {
            $ret['value'] = (2 == $externalData['without_euro_key_access']);
        }

        return $ret;
    }

    /**
     * Je pristupny zdarma
     *
     * @param array $externalData
     *
     * @return null|array
     */
    protected function getPictogramFree($externalData)
    {
        $ret = [
            'value' => null,
            'accessible' => null,
        ];

        if (isset($externalData['free'])) {
            $ret['value'] = (1 == $externalData['free']);
            $ret['accessible'] = (1 == $externalData['free']);
        }

        return $ret;
    }

    /**
     * Je bezbarierovy
     *
     * @param array $externalData
     *
     * @return null|array
     */
    protected function getPictogramAccessible($externalData)
    {
        $ret = [
            'value' => null,
            'accessible' => null,
        ];

        if (isset($externalData['accessible'])) {
            $ret['value'] = (1 == $externalData['accessible']);
            $ret['accessible'] = (1 == $externalData['accessible']);
        }

        return $ret;
    }

    /**
     * Prednostni pristup WC karta
     * obracena logika oproti importovanemu atributu no_card_priority
     *
     * @param array $externalData
     *
     * @return null|array
     */
    protected function getPictogramCardPriority($externalData)
    {
        $ret = [
            'value' => null,
            'accessible' => null,
        ];

        if (isset($externalData['no_card_priority'])) {
            $ret['value'] = (2 == $externalData['no_card_priority']);
        }

        return $ret;
    }

    /**
     * Zdarma s WC kartou
     * obracena logika oproti importovanemu atributu always_paid
     *
     * @param array $externalData
     *
     * @return null|array
     */
    protected function getPictogramCardFree($externalData)
    {
        $ret = [
            'value' => null,
            'accessible' => null,
        ];

        if (isset($externalData['always_paid'])) {
            $ret['value'] = (2 == $externalData['always_paid']);
            $ret['accessible'] = (2 == $externalData['always_paid']);
        }

        return $ret;
    }

    /**
     * Je pristupny pouze s WC kartou
     * obracena logika oproti importovanemu atributu without_card_access
     *
     * @param array $externalData
     *
     * @return null|array
     */
    protected function getPictogramWithCardAccess($externalData)
    {
        $ret = [
            'value' => null,
            'accessible' => null,
        ];

        if (isset($externalData['without_card_access'])) {
            $ret['value'] = (2 == $externalData['without_card_access']);
        }

        return $ret;
    }

    /**
     * Pristupnost WC - dle attr2
     *
     * @param integer $accessibilityId ciselnik pristupnosti objektu odpovida ciselniku pristupnosti WC
     *
     * @return bool|array
     */
    protected function getWcAccessibility($accessibilityId)
    {
        if (1 === $accessibilityId) {
            $ret = [
                'id' => 1,
                'title' => ObjectMetadata::WC_ACCESSIBILITY_OK,
            ];
        } else if (2 === $accessibilityId) {
            $ret = [
                'id' => 2,
                'title' => ObjectMetadata::WC_ACCESSIBILITY_PARTLY,
            ];
        } else {
            $ret = [
                'id' => 3,
                'title' => ObjectMetadata::WC_ACCESSIBILITY_NO,
            ];
        }

        return $ret;
    }
}
