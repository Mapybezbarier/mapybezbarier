<?php

namespace MP\Util\Latte\Filter;

use Latte\Runtime\Html;
use Misd\Linkify\Linkify;
use Nette\Http\Url;
use WebLoader\InvalidArgumentException;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FilterSet
{
    const FILTERS = [
        'linkify',
        'urlWithProtocol',
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

    /**
     * Latte filtr pro doplneni pripadne chybejiciho protokolu do URL
     *
     * @param string $url
     *
     * @return string
     */
    public function urlWithProtocol(string $url)
    {
        try {
            $url = new Url($url);

            if (!$url->getScheme()) {
                $url->setScheme('http');
            }

            $ret = $url->getAbsoluteUrl();
        } catch (InvalidArgumentException $e) {
            $ret = '';
        }

        return $ret;
    }
}
