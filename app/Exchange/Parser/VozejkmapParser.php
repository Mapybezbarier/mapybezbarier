<?php

namespace MP\Exchange\Parser;
use MP\Exchange\Exception\ParseException;
use MP\Exchange\Service\ImportLogger;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

/**
 * Parser pro data z Vozejkmap.cz
 * Jedna se o data v externim formatu - format dat je JSON
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class VozejkmapParser implements IParser
{
    /** @var array mapa ciselnikoveho atributu - typ objektu - pouze pro informativni vypis */
    protected $mapLocationType = [
        1 => 'CulturalFacilityObjectCategory', // 'Kultura',
        2 => 'SportsFacilityObjectCategory', // 'Sport'
        3 => 'InstitutionObjectCategory', // 'Instituce',
        4 => 'RestaurantObjectCategory', //'Jídlo a pití',
        5 => 'HotelObjectCategory', // 'Ubytování',
        6 => 'DoctorObjectCategory', // 'Lékaři, lékárny',
        7 => 'ServiceObjectCategory', // 'Jiné',
        8 => 'TransportObjectCategory', // 'Doprava',
        9 => 'PublicToiletObjectCategory', // 'Veřejné WC',
        10 => 'GasStationObjectCategory', // 'Benzínka',
        11 => 'StoreObjectCategory', // 'Obchod',
        12 => 'BankObjectCategory', // 'Banka, bankomat',
        13 => 'ServiceObjectCategory', // 'Parkoviště',
        14 => 'CarDealerObjectCategory', // 'Prodejní a servisní místa Škoda Auto',
        15 => 'ServiceObjectCategory', // 'Škoda Handy',
    ];

    /** @var array mapa ciselnikoveho atributu - typ bezbarierovosti - pouze pro informativni vypis */
    protected $mapAttr1 = [
        1 => 'Bez schodů',
        2 => 'Nájezd či rampa',
        3 => 'Výtah',
        4 => 'Schodišťová plošina',
        5 => 'Zdviž',
        6 => 'Nájezd či rampa + výtah',
        7 => 'Nájezd či rampa + schodišťová plošina',
        8 => 'Nájezd či rampa + zdviž',
        9 => 'Zdviž + schodišťová plošina',
        10 => 'Zdviž + výtah',
        11 => 'Výtah + schodišťová plošina',
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
        $locationTypeId = Arrays::get($row, 'location_type', null);

        $ret = [
            'title' => Arrays::get($row, 'title', null),
            'description' => Arrays::get($row, 'description', null),
            'latitude' => Arrays::get($row, 'lat', null),
            'longitude' => Arrays::get($row, 'lng', null),
            'entrance1IsReservedParking' => ('yes' === Arrays::get($row, 'attr3', 'no')),
            'objectType' => ($locationTypeId ? Arrays::get($this->mapLocationType, $locationTypeId, ObjectMetadata::CATEGORY_OTHER) : ObjectMetadata::CATEGORY_OTHER),
            'accessibility' => $this->getObjectAccessibility($row),
            'externalData' => $this->prepareExternalData($row),
            'webUrl' => Arrays::get($row, 'link', null),
            'zipcode' => Arrays::get($row, 'zip', null),
            'street' => Arrays::get($row, 'street', null),
            'city' => Arrays::get($row, 'city', null),
            'mappingDate' => time(),
        ];

        $this->parseHouseNumber($ret, $row);

        return $ret;
    }

    /**
     * zajimaji me pouze vybrana data, u nekterych s nich si ulozim i hodnotu ze znameho ciselniku
     * @param array $row
     * @return string JSON
     */
    protected function prepareExternalData($row)
    {
        $ret = [];

        $fixedKeys = ['attr2', 'attr3', 'author_name'];

        foreach ($fixedKeys as $fixedKey) {
            $ret[$fixedKey] = Arrays::get($row, $fixedKey, null);
        }

        // ciselnik location_type
        $locationTypeId = Arrays::get($row, 'location_type', null);

        if ($locationTypeId) {
            $title = Arrays::get($this->mapLocationType, $locationTypeId, null);

            if (null === $title) {
                ImportLogger::addNotice($row, 'invalidVozejkmapObjectEnumValue', ['value' => $locationTypeId, 'key' => 'location_type', 'values' => implode(', ', $this->mapLocationType)]);
            }

            $ret['location_type'] = [
                'id' => $locationTypeId,
                'title' => $title,
            ];
        }

        // ciselnik attr1
        $attr1Id = Arrays::get($row, 'attr1', null);

        if ($attr1Id) {
            $title = Arrays::get($this->mapAttr1, $attr1Id, null);

            if (null === $title) {
                ImportLogger::addNotice($row, 'invalidVozejkmapObjectEnumValue', ['value' => $attr1Id, 'key' => 'attr1', 'values' => implode(', ', $this->mapAttr1)]);
            }

            $ret['attr1'] = [
                'id' => $attr1Id,
                'title' => $title,
            ];
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
        $ret = ObjectMetadata::ACCESSIBILITY_NO;

        if (!empty($row['attr2'])) {
            $ret = 'yes' === $row['attr2'] ? ObjectMetadata::ACCESSIBILITY_OK : ObjectMetadata::ACCESSIBILITY_PARTLY;
        }

        return $ret;
    }

    /**
     * Rozparsuje cislo popisne a orientacni
     * @param array $ret
     * @param array $row
     */
    protected function parseHouseNumber(&$ret, $row)
    {
        $housenumber = Arrays::get($row, 'street_number', null);

        if ($housenumber) {
            if (preg_match('~(\d+)/?(\d*)(\D*)~', $housenumber, $matches)) {
                if ($matches[1] && !$matches[2] && $matches[3]) {
                    $ret['streetOrientNo'] = $matches[1];
                    $ret['streetOrientSymbol'] = $matches[3];
                } else {
                    $ret['streetDescNo'] = $matches[1] ? $matches[1] : null;
                    $ret['streetOrientNo'] = $matches[2] ? $matches[2] : null;
                    $ret['streetOrientSymbol'] = $matches[3] ? $matches[3] : null;
                }
            }
        }
    }
}
