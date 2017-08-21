<?php

namespace MP\Object;

use MP\Util\Arrays;

/**
 * Trida s helpery pro manipulaci s daty objektu
 */
class ObjectHelper
{
    /**
     * Sestavi adresu ve tvaru nejlepe odpovidajiciho pozadavkum API a soucasne vypisu na webu
     * Pro testovani lze vyuzit:
     *     multiple: Lhota 5
     *     zero: Unknown address
     * @param array $item
     *
     * @return string adresa tvaru "Vyskočilova 1422/1a, 140 28 Praha-Michle"
     */
    public static function getAddressString($item)
    {
        $ret = '';

        if ($item['street']) {
            $ret .= $item['street'];
        } else if ($item['city']) {
            $ret .= $item['city'];
        }

        if ($ret && $item['street_desc_no']) {
            $ret .= " {$item['street_desc_no']}";
        }

        if ($ret && $item['street_orient_no']) {
            $ret .= "/{$item['street_orient_no']}{$item['street_orient_symbol']}";
        }

        if ($ret) {
            $ret .= ",";
        }

        if ($item['zipcode']) {
            if ($ret) {
                $ret .= " ";
            }

            $ret .= $item['zipcode'];
        }

        if ($item['city']) {
            if ($ret) {
                $ret .= " ";
            }

            $ret .= $item['city'];
        }

        if ($ret && $item['city_part'] && ($item['city_part'] != $item['city'])) {
            $ret .= ", {$item['city_part']}";
        }

        return $ret;
    }

    /**
     * Vrati prilohy objektu a tyto z objektu odstrani
     *
     * @param array $object
     *
     * @return array
     */
    public static function getAttachements(array &$object)
    {
        $attachements = [];

        $attachements[ObjectMetadata::RAMP_SKIDS] = (array) Arrays::get($object, ObjectMetadata::RAMP_SKIDS, []);
        unset($object[ObjectMetadata::RAMP_SKIDS]);

        $attachements[ObjectMetadata::PLATFORM] = (array) Arrays::get($object, ObjectMetadata::PLATFORM, []);
        unset($object[ObjectMetadata::PLATFORM]);

        $attachements[ObjectMetadata::ELEVATOR] = (array) Arrays::get($object, ObjectMetadata::ELEVATOR, []);
        unset($object[ObjectMetadata::ELEVATOR]);

        $attachements[ObjectMetadata::WC] = (array) Arrays::get($object, ObjectMetadata::WC, []);
        unset($object[ObjectMetadata::WC]);

        return $attachements;
    }
}
