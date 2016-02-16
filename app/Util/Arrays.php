<?php

namespace MP\Util;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Arrays extends \Nette\Utils\Arrays
{
    /**
     * Sestavi asociativni pole klic hodnota ze dvou hodnot daneho multidimensionalniho pole.
     * Projde jednotlive prvky daneho pole a do vysledneho pole pod klic
     *
     * @param array|\Traversable $array
     * @param string $key
     * @param string $valueKey
     *
     * @return array
     */
    public static function pairs($array, $key, $valueKey)
    {
        $pairs = [];

        foreach ($array as $value) {
            if (isset($value[$key])) {
                $pairs[$value[$key]] = isset($value[$valueKey]) ? $value[$valueKey] : null;
            }
        }

        return $pairs;
    }

    /**
     * Testuje, zdali se jedna o iterovatelny objekt/pole (ktery je mozne prochazet pres foreach).
     * Pozor, v PHP je iterovatelny kazdy objekt pres jeho viditelne property (viditelne v aktualnim
     * scopu volani foreache).
     *
     * Vraci true, pokud je argument pole, nebo instance \Traversable nebo \stdClass. Jinak false
     *
     * @param mixed $array
     *
     * @return bool
     */
    public static function isIterable(&$array)
    {
        return (is_array($array) || $array instanceof \Traversable || $array instanceof \stdClass);
    }

    /**
     * Prohodi hodnoty v poli za hodnotu jejich klice. Oproti PHP array_flip podporuje jako hodnotu pole s urovni
     * zanoreni 1.
     *
     * @param array $array
     *
     * @return array
     */
    public static function flip(&$array)
    {
        $flippedArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    if (is_scalar($item)) {
                        $flippedArray[$item] = $key;
                    } else {
                        throw new \Nette\InvalidArgumentException("Can only flip STRING, INTEGER AND NOT NESTED array values");
                    }
                }
            } else if (is_scalar($value)) {
                $flippedArray[$value] = $key;
            } else {
                throw new \Nette\InvalidArgumentException("Can only flip STRING, INTEGER AND NOT NESTED array values");
            }
        }

        return $flippedArray;
    }

    /**
     * Obdoba PHP array_filter s podporovou rekurze.
     *
     * @param array $array
     * @param callable|null $callback
     *
     * @return array
     */
    public static function filter(&$array, callable $callback = null)
    {
        $array = is_callable($callback) ? array_filter($array, $callback) : array_filter($array);

        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = call_user_func(static::class . '::filter', $value, $callback);
            }
        }

        return $array;
    }
}
