<?php

namespace MP\Exchange\Parser;

use MP\Exchange\Exception\ParseException;
use MP\Exchange\Service\ImportLogger;
use MP\Manager\ExchangeSourceManager;
use MP\Manager\ObjectManager;
use MP\Object\ObjectMetadata;
use MP\Util\Address\Address;
use Nette\Utils\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Parser pro data z Wheelmap (https://www.wheelmap.org)
 * Jedna se o data v externim formatu - format dat je JSON
 * @author Jakub Vrbas
 */
class WheelmapParser implements IParser
{
    /** @var ObjectManager */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /** @var array mapa ciselnikoveho atributu - typ objektu */
    protected $mapCategory = [
        1 => 'TransportObjectCategory', // 'Public transport',
        2 => 'RestaurantObjectCategory', // 'Food'
        3 => 'LeisureTimeObjectCategory', // 'Leisure',
        4 => 'BankObjectCategory', //'Bank / Post office',
        5 => 'PublicToiletObjectCategory', // 'Education',
        6 => 'StoreObjectCategory', // 'Shopping',
        7 => 'SportsFacilityObjectCategory', // 'Sport',
        8 => 'ServiceObjectCategory', // 'Tourism',
        9 => 'HotelObjectCategory', // 'Accomodation',
        10 => 'ServiceObjectCategory', // 'Miscellaneous',
        11 => 'InstitutionObjectCategory', // 'Government',
        12 => 'MedicalFacilityObjectCategory', // 'Health',
    ];

    /** @var array mapa ciselnikoveho atributu - typ objektu */
    protected $mapWheelchair = [
        'yes' => ObjectMetadata::ACCESSIBILITY_OK,
        'limited' => ObjectMetadata::ACCESSIBILITY_PARTLY,
        'no' => ObjectMetadata::ACCESSIBILITY_NO,
    ];

    /** @var array polygon zjednodusenych hranic Ceske republiky */
    protected $crPolygon = [
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

    /** @var array mapa hashu identifikuji objekty z VozejkMap */
    protected $vozejkmapHashmap;

    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $ret = [];

        if (!is_array($data)) {
            $rows = $this->prepareNodesData($data);
        } else {
            $rows = $data;
        }

        foreach ($rows as $row) {
            if ($this->isInCr($row)) {
                if (!$this->isVozejkmapDuplicate($row)) {
                    $ret[] = $this->prepareMapObject($row);
                }
            }
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return IParser::TYPE_EXTERNAL;
    }

    /**
     * Prevede textovy JSON na pole
     * @param string $data
     * @return array
     * @throws ParseException
     */
    protected function prepareNodesData($data)
    {
        $ret = [];

        try {
            $ret = Arrays::get(Json::decode($data, Json::FORCE_ARRAY), 'nodes', []);
        } catch (JsonException $e) {
            throw new ParseException('Data nejsou korektne zformatovany JSON.');
        }

        return $ret;
    }

    /**
     * Zpracuje 1 objekt
     * @param array $row
     * @return array
     */
    protected function prepareMapObject($row)
    {
        $row['title'] = Arrays::get($row, 'name', null);

        $ret = [
            'title' => Arrays::get($row, 'name', null),
            'latitude' => Arrays::get($row, 'lat', null),
            'longitude' => Arrays::get($row, 'lon', null),
            'objectType' => $this->getAndCheckObjectType($row),
            'accessibility' => $this->getAndCheckObjectAccessibility($row),
            'externalData' => $this->prepareExternalData($row),
            'webUrl' => Arrays::get($row, 'website', null),
            'zipcode' => Arrays::get($row, 'postcode', null),
            'street' => Arrays::get($row, 'street', null),
            'city' => Arrays::get($row, 'city', null),
            'mappingDate' => time(),
        ];

        Address::parseHouseNumber($ret, $row, 'housenumber');

        return $ret;
    }

    /**
     * Prevede kategorii z externiho zdroje na interni typ objektu
     * @param array $row
     * @return string
     */
    protected function getAndCheckObjectType($row)
    {
        $ret = ObjectMetadata::CATEGORY_OTHER;

        if (!empty($row['category'])) {
            $categoryId = Arrays::get($row['category'], 'id', null);

            if (!$categoryId || !isset($this->mapCategory[$categoryId])) {
                ImportLogger::addNotice($row, 'invalidEnumValue', ['value' => $categoryId, 'key' => 'category-id', 'values' => implode(', ', array_keys($this->mapCategory))]);
            } else {
                $ret = $this->mapCategory[$categoryId];
            }
        }

        return $ret;
    }

    /**
     * zajimaji me pouze vybrana data
     * @param array $row
     * @return string JSON
     */
    protected function prepareExternalData($row)
    {
        $ret = [];

        if (isset($row['wheelchair_toilet'])) {
            $ret['wheelchair_toilet'] = $row['wheelchair_toilet'] ?: 'unknown';
        }

        $ret['id'] = Arrays::get($row, 'id', null);

        try {
            $ret = Json::encode($ret);
        } catch (JsonException $e) {
            throw new ParseException('Nepodarilo se externi data zakodovat jako JSON.');
        }

        return $ret;
    }

    /**
     * Prevede pristuponost z externiho zdroje na interni pristupnost
     * @param array $row
     * @return string
     */
    protected function getAndCheckObjectAccessibility($row)
    {
        $ret = ObjectMetadata::ACCESSIBILITY_NO;
        $wheelChairAccessibility = Arrays::get($row, 'wheelchair', null);

        if ($wheelChairAccessibility) {
            if (!isset($this->mapWheelchair[$wheelChairAccessibility])) {
                ImportLogger::addError($row, 'invalidEnumValue', ['value' => $wheelChairAccessibility, 'key' => 'wheelchair', 'values' => implode(', ', array_keys($this->mapWheelchair))]);
            } else {
                $ret = $this->mapWheelchair[$wheelChairAccessibility];
            }
        }

        return $ret;
    }

    /**
     * Vyhodnoti, zda se GPS souradnice importovaneho objektu nachazi v polygonu hranic Ceske republiky
     * Algoritmus inspirovan http://assemblysys.com/php-point-in-polygon-algorithm/ (Author: Michaël Niessen)
     * 
     * @param array $row
     * @return boolean
     */
    protected function isInCr($row)
    {
        $longitude = Arrays::get($row, 'lon', 0);
        $latitude = Arrays::get($row, 'lat', 0);

        $intersections = 0;

        for ($i = 1; $i < count($this->crPolygon); $i++) {
            $vertex1 = $this->crPolygon[$i-1];
            $vertex2 = $this->crPolygon[$i];

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
     * Overi, zda se jedna o duplicitni objekt z datasetu Vozejkmap.cz
     * @param array $row
     * @return boolean
     */
    protected function isVozejkmapDuplicate($row)
    {
        if (!isset($this->vozejkmapHashmap)) {
            $this->vozejkmapHashmap = $this->objectManager->findCompareHashes(
                [['[source_id] = %i', ExchangeSourceManager::VOZEJKMAP_ID]]
            );
        }

        $wheelmapHash = md5(Arrays::get($row, 'lon', 0) . Arrays::get($row, 'lat', 0) . Arrays::get($row, 'name', null));

        return in_array($wheelmapHash, $this->vozejkmapHashmap, true);
    }
}
