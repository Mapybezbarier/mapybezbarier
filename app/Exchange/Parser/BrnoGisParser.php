<?php

namespace MP\Exchange\Parser;

use MP\Exchange\Exception\ParseException;
use MP\Exchange\Service\ImportLogger;
use MP\Mapper\GISMapper;
use MP\Object\ObjectMetadata;
use MP\Util\Address\Address;
use MP\Util\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Parser pro data z GIS Brno (http://gis.brno.cz/arcgis/rest/services/PUBLIC/bezbarierove_objekty/MapServer)
 * Jedna se o data v externim formatu - format dat je JSON
 * @author Jakub Vrbas
 */
class BrnoGisParser implements IParser
{
    /** @var GISMapper */
    protected $gisMapper;

    /**
     * @param GISMapper $gisMapper
     */
    public function __construct(GISMapper $gisMapper)
    {
        $this->gisMapper = $gisMapper;
    }

    /** @var array mapa ciselnikoveho atributu - typ objektu */
    protected $mapCategory = [
        'divadlo, koncerty' => 'TheatreObjectCategory',
        'infocentrum' => 'InformationCenterObjectCategory',
        'kino' => 'CinemaObjectCategory',
        'knihovna' => 'LibraryObjectCategory',
        'kostel' => 'ChurchObjectCategory',
        'muzeum, galerie, kulturní památka' => 'MuseumObjectCategory',
        'úřad' => 'InstitutionObjectCategory',
        'pošta' => 'PostOfficeObjectCategory',
        'banka, pojišťovna' => 'BankObjectCategory',
        'bankomat' => 'AtmObjectCategory',
        'obchod' => 'StoreObjectCategory',
        'kavárna' => 'PastryObjectCategory',
        'restaurace' => 'RestaurantObjectCategory',
        'hotel' => 'HotelObjectCategory',
        'bazén' => 'IndoorSwimmingPoolObjectCategory',
        'lékárna' => 'PharmacyObjectCategory',
        'ordinace, nemocnice' => 'MedicalFacilityObjectCategory',
        'WC' => 'PublicToiletObjectCategory',
        'terminál hromadné dopravy' => 'TransportObjectCategory',
        'park' => 'LeisureTimeObjectCategory',
    ];

    protected $mapAccessibility = [
        'přístupné' => ObjectMetadata::ACCESSIBILITY_OK,
        'přístupné s asistencí' => ObjectMetadata::ACCESSIBILITY_PARTLY,
        'nepřístupné' => ObjectMetadata::ACCESSIBILITY_NO,
        'přístupné - dočasně zavřeno' => ObjectMetadata::ACCESSIBILITY_OK,
        'přístupné s asistencí - dočasně zavřeno' => ObjectMetadata::ACCESSIBILITY_PARTLY,
        'nepřístupné - dočasně zavřeno' => ObjectMetadata::ACCESSIBILITY_NO,
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
                $rows = Json::decode($data, Json::FORCE_ARRAY)['features'];
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
        $attributes = Arrays::get($row, 'properties', []);
        $row['title'] = Arrays::get($attributes, 'nazev_cz', null);

        $ret = [
            'title' => Arrays::get($attributes, 'nazev_cz', null),
            'description' => Arrays::get($attributes, 'popis_cz', null),
            'objectType' => $this->getObjectType($attributes),
            'accessibility' => $this->getObjectAccessibility($attributes),
            'city' => 'Brno',
            'webUrl' => Arrays::get($attributes, 'web_url', null),
            'externalData' => $this->prepareExternalData($attributes),
            'mappingDate' => (isset($attributes['aktualizace']) ? strtotime($attributes['aktualizace']) : time()),
        ];

        Address::parseStreetAndHouseNumber($ret, $attributes, 'adresa');
        $this->prepareGps($ret, Arrays::get($row, 'geometry', []));

        return $ret;
    }

    /**
     * zajimaji me pouze vybrana data
     * @param array $attributes
     * @return string JSON
     */
    protected function prepareExternalData($attributes)
    {
        $ret = [
            'id' => Arrays::get($attributes, 'ogcfid', null),
            'local_type' => Arrays::get($attributes, 'typ_budovy', null),
            'ID_ZZ' => Arrays::get($attributes, 'ogcfid', null),
            'cislo_zdravotnickeho_zarizeni' => Arrays::get($attributes, 'cislo_zdravotnickeho_zarizeni', null),
            'standard_pictograms' => [],
        ];

        for ($i = 1; $i <= 14; $i++) {
            if ($val = Arrays::get($attributes, sprintf('pikto_%02d', $i), null)) {
                $pictogramBool = ('A' === $val);
            } else {
                $pictogramBool = null;
            }

            $ret['standard_pictograms'][$i-1] = $pictogramBool;
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
     * @param array $attributes
     * @return string kod pristupnosti
     */
    protected function getObjectAccessibility($attributes)
    {
        $val = Arrays::get($attributes, 'pristupnost_budovy', null);
        $ret = Arrays::get($this->mapAccessibility, $val, ObjectMetadata::ACCESSIBILITY_NO);

        return $ret;
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
            $attributes['title'] = Arrays::get($attributes, 'nazev_cz', null);
            ImportLogger::addNotice($attributes, 'invalidBrnoGisObjectType', ['value' => $categoryId]);
        }

        return $ret;
    }

    /**
     * Transformace S-JTSKT souradnic na GPS
     * @param array $ret
     * @param array $geometry
     */
    protected function prepareGps(&$ret, $geometry)
    {
        $x = Arrays::get($geometry['coordinates'], 0, null);
        $y = Arrays::get($geometry['coordinates'], 1, null);

        if ($x && $y) {
            $ret['longitude'] = $x;
            $ret['latitude'] = $y;
        }
    }
}
