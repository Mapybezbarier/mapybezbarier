<?php

namespace MP\Service;

/**
 * Sestavi restriktor pro vypis objektu v mape.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectRestrictorBuilder
{
    /** @const Klice dostupnych restrikci */
    const
        RESTRICTION_ACCESSIBILITY_TYPE = 'accessibilityType',
        RESTRICTION_ACCESSIBILITY = 'accessibility',
        RESTRICTION_CATEGORY = 'category',
        RESTRICTION_TYPE = 'type';

    /**
     * Pripravi restrikce pro pristupnost mapovych objektu.
     *
     * @param array $accessibility
     *
     * @return array
     */
    public function prepareTypedAccessibilityRestrictions(string $accesibilityType, array $accessibility)
    {
        $restrictions = [];
        switch ($accesibilityType) {
            case FilterService::ACCESSIBILITY_TYPE_DEFAULT:
                $restrictions = ['[accessibility_id] IN %in', $accessibility];
                break;
            case FilterService::ACCESSIBILITY_TYPE_PRAM:
                $restrictions = ['([accessibility_pram_id] IS NOT NULL AND [accessibility_pram_id] IN %in) OR ([accessibility_pram_id] IS NULL AND [accessibility_id] IN %in)', $accessibility, $accessibility];
                break;
            case FilterService::ACCESSIBILITY_TYPE_SENIORS:
                $restrictions = ['([accessibility_seniors_id] IS NOT NULL AND [accessibility_seniors_id] IN %in) OR ([accessibility_seniors_id] IS NULL AND [accessibility_id] IN %in)', $accessibility, $accessibility];
                break;
        }

        return $restrictions;
    }

    /**
     * Pripravi restrikce pro pristupnost mapovych objektu.
     *
     * @param array $accessibility
     *
     * @return array
     */
    public function prepareAccessibilityRestrictions(array $accessibility)
    {
        $restrictions = ["[accessibility_id] IN %in", $accessibility];

        return $restrictions;
    }

    /**
     * Pripravi restrikce pro kategorie mapovych objektu.
     *
     * @param array $categories
     *
     * @return array
     */
    public function prepareCategoryRestrictions(array $categories)
    {
        $restrictions = ["[object_type_id] IN %in", $categories];

        return $restrictions;
    }

    /**
     * Pripravi restrikce pro typ mapoveho podkladu.
     *
     * @param array $types
     *
     * @return array
     */
    public function prepareTypeRestrictions(array $types)
    {
        $types = array_flip($types);

        $restrictions = [];

        if (isset($types[FilterService::TYPE_CERTIFIED])) {
            $restrictions[] = $this->getCertifiedRestriction();
        }

        if (isset($types[FilterService::TYPE_OUTDATED])) {
            $restrictions[] = $this->getOutdatedRestriction();
        }

        if (isset($types[FilterService::TYPE_COMMUNITY])) {
            $restrictions[] = $this->getCommunityRestriction();
        }

        if (1 < count($restrictions)) {
            $restrictions = ['%or', $restrictions];
        }

        return $restrictions;
    }

    /**
     * Vrati restrikci pro ceritifikovane zaznamy.
     *
     * @return array
     */
    public function getCertifiedRestriction()
    {
        $restriction = [
            '%and', [
                "[certified]",
                "[mapping_date] IS NOT NULL",
                "[mapping_date] > (CURRENT_TIMESTAMP - INTERVAL '10 years')",
            ],
        ];

        return $restriction;
    }

    /**
     * Vrati restrikci pro ceritifikovane, ale zastarale zaznamy.
     *
     * @return array
     */
    public function getOutdatedRestriction()
    {
        $restriction = [
            '%and', [
                "[certified]",
                [
                    '%or', [
                    "[mapping_date] IS NULL",
                    "[mapping_date] <= (CURRENT_TIMESTAMP - INTERVAL '10 years')",
                ],
                ],
            ],
        ];

        return $restriction;
    }

    /**
     * Vrati restrikci pro komunitni zaznamy.
     *
     * @return array
     */
    public function getCommunityRestriction()
    {
        $restriction = ["NOT [certified]"];

        return $restriction;
    }
}
