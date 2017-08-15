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
    protected static $mapLocationType = [
        1 => 'Kultura',
        2 => 'Sport',
        3 => 'Instituce',
        4 => 'Jídlo a pití',
        5 => 'Ubytování',
        6 => 'Lékaři, lékárny',
        7 => 'Jiné',
        8 => 'Doprava',
        9 => 'Veřejné WC',
        10 => 'Benzínka',
        11 => 'Obchod',
        12 => 'Banka, bankomat',
        13 => 'Parkoviště',
        14 => 'Prodejní a servisní místa Škoda Auto',
        15 => 'Škoda Handy',
    ];

    /** @var array mapa ciselnikoveho atributu - typ bezbarierovosti - pouze pro informativni vypis */
    protected static $mapAttr1 = [
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
            'entrance1IsReservedParking' => ('yes' === Arrays::get($row, 'lng', 'no')),
            'objectType' => ObjectMetadata::CATEGORY_OTHER,
            'objectTypeCustom' => ($locationTypeId ? Arrays::get($this->mapLocationType, $locationTypeId, null) : null),
            'accessibility' => ObjectMetadata::ACCESSIBILITY_OK,
            'externalData' => $this->prepareExternalData($row),
            'mappingDate' => time(),
        ];

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
}
