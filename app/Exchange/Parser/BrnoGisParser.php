<?php

namespace MP\Exchange\Parser;

use MP\Exchange\Exception\ParseException;
use MP\Exchange\Service\ImportLogger;
use MP\Object\ObjectMetadata;
use MP\Util\Address\Address;
use MP\Util\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Tracy\Debugger;

/**
 * Parser pro data z GIS Brno (http://gis.brno.cz/arcgis/rest/services/PUBLIC/bezbarierove_objekty/MapServer)
 * Jedna se o data v externim formatu - format dat je JSON
 * @author Jakub Vrbas
 */
class BrnoGisParser implements IParser
{
    /** @var array mapa ciselnikoveho atributu - typ objektu */
    protected $mapCategory = [
        'divadlo / koncerty' => 'TheatreObjectCategory',
        'infocentrum' => 'InformationCenterObjectCategory',
        'kino' => 'CinemaObjectCategory',
        'knihovna' => 'LibraryObjectCategory',
        'kostel' => 'ChurchObjectCategory',
        'muzeum / galerie / kulturní památka' => 'MuseumObjectCategory',
        'veřejná instituce' => 'InstitutionObjectCategory',
        'pošta' => 'PostOfficeObjectCategory',
        'banka / pojišťovna' => 'BankObjectCategory',
        'bankomat' => 'AtmObjectCategory',
        'obchod' => 'StoreObjectCategory',
        'kavárna' => 'PastryObjectCategory',
        'restaurace' => 'RestaurantObjectCategory',
        'hotel' => 'HotelObjectCategory',
        'bazén' => 'IndoorSwimmingPoolObjectCategory',
        'lékárna' => 'PharmacyObjectCategory',
        'ordinace / nemocnice' => 'MedicalFacilityObjectCategory',
        'wc' => 'PublicToiletObjectCategory',
        'terminál hromadné dopravy' => 'TransportObjectCategory',
    ];

    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $ret = [];

        if (!is_array($data)) {
            try {
                $rows = Json::decode($data, Json::FORCE_ARRAY);
            } catch (JsonException $e) {
                throw new ParseException('Data nejsou korektne zformatovany JSON.');
            }
        } else {
            $rows = $data;
        }

        foreach ($rows as $row) {
            $ret[] = $this->prepareMapObject($row);
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
        $attributes = Arrays::get($row, 'attributes', []);
        $row['title'] = Arrays::get($attributes, 'nazev_CZ', null);

        $ret = [
            'title' => Arrays::get($attributes, 'nazev_CZ', null),
            'description' => $this->getDescription($attributes),
            'objectType' => $this->getObjectType($attributes),
            'accessibility' => $this->getObjectAccessibility($attributes),
            'externalData' => $this->prepareExternalData($attributes),
            'mappingDate' => (isset($attributes['aktualiz']) ? $attributes['aktualiz']/1000 : time()),
        ];

        return $ret;
    }

    /**
     * zajimaji me pouze vybrana data
     * @param array $attributes
     * @return string JSON
     */
    protected function prepareExternalData($attributes)
    {
        $ret = [];

        return $ret;
    }

    /**
     * Detekce pristupnosti objektu
     * @param array $attributes
     * @return string kod pristupnosti
     */
    protected function getObjectAccessibility($attributes)
    {
        return ObjectMetadata::ACCESSIBILITY_NO;
    }

    /**
     * @param array $attributes
     * @return string
     */
    protected function getObjectType($attributes)
    {
        $ret = ObjectMetadata::CATEGORY_OTHER;

        $categoryId = Arrays::get($attributes, 'typ_budovy', null);

        if (isset($this->mapCategory[$categoryId])) {
            $ret = $this->mapCategory[$categoryId];
        } else {
            ImportLogger::addNotice($attributes, 'invalidBrnoGisObjectType', ['value' => $categoryId]);
        }

        return $ret;
    }

    /**
     * @param array $attributes
     * @return string
     */
    protected function getDescription($attributes)
    {
        $ret = '';
        return $ret;
    }
}
