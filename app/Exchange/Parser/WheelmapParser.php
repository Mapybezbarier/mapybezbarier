<?php

namespace MP\Exchange\Parser;

use MP\Exchange\Exception\ParseException;
use MP\Exchange\Service\ImportLogger;
use MP\Manager\ExchangeSourceManager;
use MP\Manager\ObjectManager;
use MP\Object\ObjectMetadata;
use MP\Util\Address\Address;
use MP\Util\Arrays;
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

    /** @var array mapa hashu identifikuji objekty z VozejkMap */
    protected $vozejkmapHashmap;

    /** @var array interni typy ibjektu dle OpenStreetMap */
    protected $nodeTypes;
    
    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $ret = [];

        if (!is_array($data)) {
            throw new ParseException('Wheelmap neumoznuje import ze souboru.');
        } else {
            $rows = $data['nodes'];
            $this->nodeTypes = Arrays::pairs($data['node_types'], 'id', 'localized_name');
        }

        foreach ($rows as $row) {
            $latitude = Arrays::get($row, 'lat', 0);
            $longitude = Arrays::get($row, 'lon', 0);

            if (Address::isInCr($latitude, $longitude)) {
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

        if (isset($row['node_type']['id'])) {
            $ret['node_type'] = Arrays::get($this->nodeTypes, $row['node_type']['id'], null);
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
