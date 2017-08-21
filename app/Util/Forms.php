<?php

namespace MP\Util;

use Nette\Utils\Html;

/**
 * Helper pro praci s formulari.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Forms
{
    /**
     * Pripravi hodnoty pole pro select box.
     *
     * @param $array
     * @param string $key
     * @param string $valueKey
     *
     * @return array
     */
    public static function toSelect($array, $key, $valueKey = null)
    {
        $selectValues = [];

        if (is_callable($valueKey)) {
            $values = [];

            foreach ($array as $value) {
                if (isset($value[$key])) {
                    $values[$value[$key]] = $valueKey($value);
                }
            }
        } else {
            $values = Arrays::pairs($array, $key, $valueKey ?: $key);
        }

        foreach ($values as $index => $value) {
            $selectValues[$index] = Html::el()->setText($value);
        }

        return $selectValues;
    }
}
