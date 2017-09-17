<?php

namespace MP\Util\Latte\Filter;

use Latte\Runtime\Html;
use Misd\Linkify\Linkify;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FilterSet
{
    const FILTERS = [
        'linkify'
    ];

    /**
     * Latte filtr pro vytvoreni <a> tagu v retezci obsahujicim platnou URI.
     *
     * @param string $string
     * @param array $attributes
     *
     * @return Html
     */
    public function linkify(string $string, array $attributes = [])
    {
        $linkify = new Linkify(['attr' => $attributes]);

        return new Html($linkify->processUrls($string));
    }
}
