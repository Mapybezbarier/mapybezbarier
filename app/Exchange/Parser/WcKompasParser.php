<?php

namespace MP\Exchange\Parser;

use MP\Exchange\Exception\ParseException;
use MP\Exchange\Service\ImportLogger;
use MP\Object\ObjectMetadata;
use MP\Util\Address\Address;
use MP\Util\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Parser pro data z WC Kompas (https://www.wckompas.cz/opendata.json)
 * Jedna se o data v externim formatu - format dat je JSON
 * @author Jakub Vrbas
 */
class WcKompasParser implements IParser
{
    const DESCRIPTION_ATTRIBUTE_ID = 250;
    const ACCESSIBILITY_ATTRIBUTE_ID = 254;

    /** @var array mapa ciselnikoveho atributu - typ objektu */
    protected $mapCategory = [
        140 => 'PoliceObjectCategory', // 'policie ČR',
        118 => 'InstitutionObjectCategory', // 'Úřad'
        117 => 'RestaurantObjectCategory', // 'Restaurace',
        116 => 'PublicToiletObjectCategory', //'Euroklíč',
        115 => 'PublicToiletObjectCategory', // 'veřejné WC',
        139 => 'MedicalFacilityObjectCategory', // 'zdravotnické zařízení',
    ];

    /** @var array mapa externich atributu
     * Zadne atributy nejsou povinne, proto neni mozne kontrolovat jejich pritomnost.
     * Zaroven se v datasetu vyskytuji i atributy, ktere nejsou ve specifikaci => nelze automaticky kontrolovat, zda se nezmanila jejich ID
     */
    protected $mapAttribute = [
        251 => 'without_card_access',
        252 => 'always_paid',
        253 => 'no_card_priority',
        self::ACCESSIBILITY_ATTRIBUTE_ID => 'accessible',
        255 => 'free',
        256 => 'without_euro_key_access',
    ];

    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $ret = [];

        try {
            $rows = Json::decode($data, Json::FORCE_ARRAY);
        } catch (JsonException $e) {
            throw new ParseException('Data nejsou korektne zformatovany JSON.');
        }

        if (!isset($rows['places'])) {
            throw new ParseException('Data nejsou v ocekávanem formatu.');
        }

        foreach ($rows['places'] as $row) {
            $latitude = Arrays::get($row, 'lat', 0.0);
            $longitude = Arrays::get($row, 'lng', 0.0);

            if (Address::isInCr($latitude, $longitude)) {
                $object = $this->prepareMapObject($row);

                if ($object) {
                    $ret[] = $object;
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
        $categoryId = Arrays::get($row, 'category_id', null);
        $row['title'] = Arrays::get($row, 'name', null);

        $ret = [
            'title' => Arrays::get($row, 'name', null),
            'description' => $this->getRowAttribute($row, self::DESCRIPTION_ATTRIBUTE_ID),
            'latitude' => Arrays::get($row, 'lat', null),
            'longitude' => Arrays::get($row, 'lng', null),
            'objectType' => ($categoryId ? Arrays::get($this->mapCategory, $categoryId, ObjectMetadata::CATEGORY_OTHER) : ObjectMetadata::CATEGORY_OTHER),
            'accessibility' => $this->getObjectAccessibility($row),
            'externalData' => $this->prepareExternalData($row),
            'mappingDate' => time(),
        ];

        if (empty($ret['latitude']) || empty($ret['longitude'])) {
            ImportLogger::addNotice($row, 'invalidWCKompasGPS');
            $ret = null;
        }

        return $ret;
    }

    /**
     * zajimaji me pouze vybrana data, ulozim i hodnotu ze znameho ciselniku
     * @param array $row
     * @return string JSON
     */
    protected function prepareExternalData($row)
    {
        $ret = [];

        if (!empty($row['attributes'])) {
            foreach($row['attributes'] as $attribute) {
                $attributeKey = Arrays::get($attribute, 'attribute_id', null);

                if (array_key_exists($attributeKey, $this->mapAttribute)) {
                    $arrayValue = Arrays::get($attribute, 'value', []);
                    $ret[$this->mapAttribute[$attributeKey]] = array_pop($arrayValue);
                }
            }
        }

        try {
            $ret = Json::encode($ret);
        } catch (JsonException $e) {
            throw new ParseException('Nepodarilo se externi data zakodovat jako JSON.');
        }

        return $ret;
    }

    /**
     * Detekce pristupnosti objektu
     * @param array $row
     * @return string kod pristupnosti
     */
    protected function getObjectAccessibility($row)
    {
        $accesibilityValue = $this->getRowAttribute($row, self::ACCESSIBILITY_ATTRIBUTE_ID);

        return (1 == $accesibilityValue) ? ObjectMetadata::ACCESSIBILITY_PARTLY : ObjectMetadata::ACCESSIBILITY_NO;
    }

    /**
     * Dohledani atributu objektu s danym ID
     * @param array $row
     * @param int $attributeId
     * @return mixed hodnota atributu
     */
    protected function getRowAttribute($row, $attributeId)
    {
        $ret = null;

        if (!empty($row['attributes'])) {
            foreach($row['attributes'] as $attribute) {
                if (Arrays::get($attribute, 'attribute_id', null) == $attributeId) {
                    $ret = Arrays::get($attribute, 'value', null);
                    break;
                }
            }
        }

        return $ret;
    }
}
