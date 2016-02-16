<?php

namespace MP\Util;

/**
 * Trida s helpery pro manipulaci s retezci.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Strings extends \Nette\Utils\Strings
{
    /**
     * Prevede CamelCaseZpusob na camel_case_zpusob zapisu.
     * Napr. McDonald => mc_donald, MCDonald => m_c_donald
     *
     * @param string $string vstupni retezec
     * @param string $separator separator (vychozi podtrzitko)
     *
     * @return string transformovany retezec do podtrzitkove_notace
     */
    public static function toUnderscore($string, $separator = '_')
    {
        $string = self::firstLower($string);

        return self::replace($string, "~([[:upper:]])~", function ($reference) use ($separator) {
            return $separator . self::lower($reference[1]);
        });
    }

    /**
     * Prevede podtrzitkovy_zapis na podtrzitkovyZapis.
     *
     * Jedna se o inverzni funkci k self::toUnderscore().
     *
     * @param string $s vstupni retezec
     * @param string $separator oddelovac, ktery bude odstranen a slouzi jako oddelovac slov
     * @param bool $capitalizeFirstLetter prvni pismeno velke
     *
     * @return string
     */
    public static function toCamelCase($s, $separator = '_', $capitalizeFirstLetter = false)
    {
        if ($capitalizeFirstLetter) {
            $s = self::firstUpper($s);
        }

        return self::replace($s, "~{$separator}([[:lower:]])~", function ($reference) {
            return self::upper($reference[1]);
        });
    }

    /**
     * Prevede retezec do "bezpecne" podoby pro pouziti jako slag v url:
     * "Zlutoucky kůň" => "zlutoucky-kun" (pokud $dash = true: "zlutoucky_kun")
     *
     * @param string $string
     * @param string $charlist
     * @param bool $lower
     * @param bool $dash pouzije _ misto - jako "oddelovac" slov
     *
     * @return string
     */
    public static function webalize($string, $charlist = null, $lower = true, $dash = false)
    {
        $sep = $dash ? '_' : '-';
        $string = self::toAscii($string);

        if ($lower) {
            $string = strtolower($string);
        }

        $string = preg_replace('#[^a-z0-9' . preg_quote($charlist, '#') . ']+#i', $sep, $string);
        $string = trim($string, $sep);

        return $string;
    }
}
