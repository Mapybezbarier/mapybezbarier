<?php

namespace MP\Util\Address;

use MP\Util\Strings;
use Nette\Utils\Arrays;

/**
 * Helper pro parsovani adresy
 */
class Address
{
    /**
     * Rozparsuje cislo popisne a orientacni
     * @param array $ret
     * @param array $row
     */
    public static function parseHouseNumber(&$ret, $row, $key)
    {
        $housenumber = Arrays::get($row, $key, null);

        if ($housenumber) {
            $matches = Strings::match($housenumber, '~(\d+)/?(\d*)(\D*)~');

            if ($matches) {
                if (!empty($matches[1]) && empty($matches[2]) && !empty($matches[3])) {
                    $ret['streetOrientNo'] = $matches[1];
                    $ret['streetOrientSymbol'] = $matches[3];
                } else {
                    $ret['streetDescNo'] = !empty($matches[1]) ? $matches[1] : null;
                    $ret['streetOrientNo'] = !empty($matches[2]) ? $matches[2] : null;
                    $ret['streetOrientSymbol'] = !empty($matches[3]) ? $matches[3] : null;
                }
            }
        }
    }
}
