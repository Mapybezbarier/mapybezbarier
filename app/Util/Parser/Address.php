<?php

namespace MP\Util\Address;

use MP\Util\Strings;
use Nette\Utils\Arrays;

/**
 * Helper pro parsovani adresy
 */
class Address
{

    /** @var array polygon zjednodusenych hranic Ceske republiky */
    private static $crPolygon = [
        [18.8512452096,49.5173542465],
        [18.5659002466,49.4936731907],
        [17.7122181579,48.8561090883],
        [17.1879091581,48.8694451992],
        [16.9461822783,48.619064172],
        [16.5405541546,48.8123542125],
        [16.1033361464,48.7500000611],
        [15.0286091888,49.0187451677],
        [14.7002821615,48.5813821527],
        [13.8336092964,48.7736090326],
        [12.6744452253,49.4250002578],
        [12.4555543378,49.6955451266],
        [12.5459731127,49.9095822216],
        [12.0937002006,50.3225361287],
        [12.3230542179,50.206664196],
        [12.5154180581,50.3924911575],
        [12.985554147,50.4183272007],
        [14.3113911898,50.8822181011],
        [14.3062452041,51.0524911002],
        [14.8283361848,50.8658271218],
        [15.1769450708,51.0147180534],
        [15.379718229,50.7794450715],
        [16.3320092179,50.6640272702],
        [16.447363149,50.5788180158],
        [16.2190271977,50.4102731975],
        [16.6400002392,50.1088911392],
        [17.002218151,50.2169451031],
        [16.8909731965,50.4386730972],
        [17.7244451754,50.3190271287],
        [17.6577731704,50.1080541223],
        [18.578745178,49.9122181625],
        [18.8512452096,49.5173542465],
    ];

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

    /**
     * Vyhodnoti, zda se GPS souradnice importovaneho objektu nachazi v polygonu hranic Ceske republiky
     * Algoritmus inspirovan http://assemblysys.com/php-point-in-polygon-algorithm/ (Author: Michaël Niessen)
     *
     * @param float $latitude zemepisna sirka
     * @param float $longitude zemepisna delka
     * @return boolean
     */
    public static function isInCr($latitude, $longitude)
    {
        $intersections = 0;

        for ($i = 1; $i < count(self::$crPolygon); $i++) {
            $vertex1 = self::$crPolygon[$i-1];
            $vertex2 = self::$crPolygon[$i];

            if (
                $latitude > min($vertex1[1], $vertex2[1])
                && $latitude <= max($vertex1[1], $vertex2[1])
                && $longitude <= max($vertex1[0], $vertex2[0])
            ) {
                $xinters = ($latitude - $vertex1[1]) * ($vertex2[0] - $vertex1[0]) / ($vertex2[1] - $vertex1[1]) + $vertex1[0];

                if ($longitude <= $xinters) {
                    $intersections++;
                }
            }
        }

        return ($intersections % 2 != 0);
    }

    /**
     * Rozparsuje GPS souradnice ve tvaru 48.9757850N, 14.4717033E
     * @param array $ret
     * @param array $row
     */
    public static function parseDecimalGps(&$ret, $row, $key)
    {
        $coordinates = Arrays::get($row, $key, null);

        if ($coordinates) {
            $matches = Strings::match($coordinates, '~(\d+.\d+)\w?,\s?(\d+.\d+)\w?~');

            if ($matches) {
                $ret['latitude'] = $matches[1];
                $ret['longitude'] = $matches[2];
            }
        }
    }

}
