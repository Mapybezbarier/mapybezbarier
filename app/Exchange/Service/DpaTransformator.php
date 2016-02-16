<?php

namespace MP\Exchange\Service;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use MP\Exchange\Export\CsvResolver\CsvResolver;
use MP\Object\ObjectMetadata;
use MP\Util\Strings;
use Nette\Utils\DateTime;
use Nette\Utils\Finder;

/**
 * Jednoucelovy skript pro prevod z CSV ve formatu z DPA do interniho CSV formatu
 * Bez osetreni chyb, znovupouzitelnosti.
 */
class DpaTransformator
{
    private static $relations = [
        ObjectMetadata::ELEVATOR => [136, 177, 218],
        ObjectMetadata::PLATFORM => [539, 571, 603, 635],
        ObjectMetadata::RAMP_SKIDS => [667, 702, 737, 772, 807, 842],
        ObjectMetadata::WC => [259, 315, 371, 427, 483],
    ];

    /**
     * @var array
     * Mapa cisla sloupce z CSV DPA na cislo sloupce v internim CSV formatu
     */
    private static $map = [
        'main' => [
            2 => 7,
            3 => 24,
            4 => 25,
            5 => 20,
            6 => 22,
            7 => 21,
            8 => 15,
            9 => 10,
            10 => 28,
            11 => 19,
            12 => 18,
            13 => 17,
            14 => 13,
            15 => 14,


            18 => 31,
            19 => 30,
            20 => 32,
            21 => 39,
            22 => 40,
            23 => 33,
            24 => 35,
            25 => 34,
            26 => 36,
            27 => 38,
            28 => 37,
            29 => 41,
            30 => 42,
            31 => 56,
            32 => 57,
            33 => 55,
            34 => 44,
            35 => 43,
            36 => 52,
            37 => 53,
            38 => 54,
            39 => 47,
            40 => 48,
            41 => 49,
            42 => 66,
            43 => 67,
            44 => 68,
            45 => 64,
            46 => 65,
            47 => 69,
            48 => 45,
            49 => 46,
            50 => 72,
            51 => 73,
            52 => 74,
            53 => 70,
            54 => 71,
            55 => 75,
            56 => 58,
            57 => 59,
            58 => 60,
            59 => 61,
            60 => 62,
            61 => 63,
            62 => 50,
            63 => 78,
            64 => 76,
            65 => 77,
            66 => 83,
            67 => 82,
            68 => 84,
            69 => 91,
            70 => 92,
            71 => 85,
            72 => 87,
            73 => 86,
            74 => 88,
            75 => 90,
            76 => 89,
            77 => 93,
            78 => 79,
            79 => 80,
            80 => 94,
            81 => 108,
            82 => 109,
            83 => 107,
            84 => 96,
            85 => 95,
            86 => 104,
            87 => 105,
            88 => 106,
            89 => 99,
            90 => 100,
            91 => 101,
            92 => 118,
            93 => 119,
            94 => 120,
            95 => 116,
            96 => 117,
            97 => 121,
            98 => 97,
            99 => 98,
            100 => 124,
            101 => 125,
            102 => 126,
            103 => 122,
            104 => 123,
            105 => 127,
            106 => 110,
            107 => 111,
            108 => 112,
            109 => 113,
            110 => 114,
            111 => 115,
            112 => 102,
            113 => 137,
            114 => 138,
            115 => 139,
            116 => 140,
            117 => 141,
            118 => 147,
            119 => 148,
            120 => 150,
            121 => 151,
            122 => 152,
            123 => 142,
            124 => 143,
            125 => 144,
            126 => 145,
            127 => 128,
            128 => 129,
            129 => 130,
            130 => 131,
            131 => 132,
            132 => 133,
            133 => 134,
            134 => 135,
            135 => 136,
        ],
        ObjectMetadata::ELEVATOR => [
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
            6 => 6,
            7 => 36,
            8 => 37,
            9 => 38,
            10 => 39,
            11 => 40,
            12 => 41,
            13 => 42,
            14 => 7,
            15 => 8,
            16 => 9,
            17 => 10,
            18 => 11,
            19 => 12,
            20 => 13,
            21 => 14,
            22 => 16,
            23 => 17,
            24 => 15,
            25 => 18,
            26 => 19,
            27 => 20,
            28 => 21,
            29 => 22,
            30 => 23,
            31 => 24,
            32 => 25,
            33 => 26,
            34 => 27,
            35 => 28,
            36 => 29,
            37 => 30,
            38 => 31,
            39 => 33,
            40 => 32,
            41 => 35,
        ],
        ObjectMetadata::WC => [
            1 => 2,
            2 => 35,
            3 => 36,
            4 => 37,
            5 => 38,
            6 => 39,
            7 => 40,
            8 => 41,
            9 => 42,
            10 => 4,
            11 => 3,
            12 => 5,
            13 => 49,
            14 => 50,
            15 => 52,
            16 => 51,
            19 => 8,
            20 => 9,
            21 => 16,
            22 => 17,
            23 => 19,
            24 => 18,
            25 => 21,
            26 => 20,
            27 => 10,
            28 => 11,
            29 => 12,
            30 => 13,
            31 => 14,
            32 => 43,
            33 => 45,
            34 => 44,
            35 => 46,
            36 => 48,
            37 => 47,
            38 => 15,
            39 => 53,
            40 => 54,
            41 => 60,
            42 => 59,
            43 => 55,
            44 => 56,
            45 => 57,
            46 => 58,
            47 => 25,
            48 => 26,
            49 => 27,
            50 => 22,
            51 => 24,
            52 => 23,
            53 => 28,
            54 => 29,
            55 => 34,
            56 => 1,
        ],
        ObjectMetadata::PLATFORM => [
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 6,
            6 => 7,
            7 => 5,
            8 => 8,
            9 => 16,
            10 => 19,
            11 => 20,
            12 => 17,
            13 => 18,
            14 => 21,
            15 => 22,
            16 => 23,
            17 => 24,
            18 => 25,
            19 => 28,
            20 => 29,
            21 => 26,
            22 => 27,
            23 => 30,
            24 => 31,
            25 => 32,
            26 => 33,
            27 => 11,
            28 => 12,
            29 => 13,
            30 => 9,
            31 => 10,
            32 => 15,
        ],
        ObjectMetadata::RAMP_SKIDS => [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 5,
            5 => 4,
            6 => 6,
            7 => 7,
            8 => 10,
            9 => 8,
            10 => 9,
            11 => 13,
            12 => 11,
            13 => 12,
            14 => 16,
            15 => 14,
            16 => 15,
            17 => 19,
            18 => 17,
            19 => 18,
            20 => 22,
            21 => 23,
            22 => 20,
            23 => 21,
            24 => 24,
            25 => 25,
            26 => 26,
            27 => 27,
            28 => 29,
            29 => 30,
            30 => 31,
            31 => 32,
            32 => 33,
            33 => 35,
            34 => 34,
            35 => 37,
        ],
    ];

    /**
     * @var array
     * Nahrazeni hodnot konkretnich sloupcu
     * Klicem je internalKey a hodnotou pole prevodu
     */
    private static $convertValues = [
        'main' => [
            144 => [
                'PŘÍSTUPNÝ celý' => ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE,
                'PŘÍSTUPNÁ větší část' => ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE,
                'PŘÍSTUPNÁ menší část' => ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_PART,
                'NEPŘÍSTUPNÝ celý' => ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_INACCESSIBLE,
            ],
            56 => ['0' => null], //entrance1Steps1Height
            59 => ['0' => null], //entrance1Steps2Height
            108 => ['0' => null], //entrance2Steps1Height
            111 => ['0' => null], //entrance2Steps2Height
            130 => ['0' => null], //objectSteps1Height,
            85 => ['a' => true, 'n' => false], //entrance2IsLongitudinalInclination
            88 => ['a' => true, 'n' => false], //entrance2IsTransverseInclination
            113 => ['a' => true, 'n' => false], //entrance2ContrastMarkingIsGlassSurfaces
            115 => ['a' => true, 'n' => false], //entrance2StepsIsContrastMarked
            148 => ['chybí' => null], //objectContrastMarkingLocalization
            68 => [ //entrance1Door1OpeningDirection
                'ven' => 'OutwardsDoorOpeningDirection',
                'dovnitř' => 'InwardsDoorOpeningDirection',
            ],
            120 => [ //entrance2Door1OpeningDirection
                'ven' => 'OutwardsDoorOpeningDirection',
                'dovnitř' => 'InwardsDoorOpeningDirection',
            ],
            74 => [ //entrance1Door2OpeningDirection
                'ven' => 'OutwardsDoorOpeningDirection',
                'dovnitř' => 'InwardsDoorOpeningDirection',
            ],
            126 => [ //entrance2Door2OpeningDirection
                'ven' => 'OutwardsDoorOpeningDirection',
                'dovnitř' => 'InwardsDoorOpeningDirection',
            ],
            79 => [ // entrance2Access
                'volně přístupný' => 'FreelyaccessibleMappableEntityAccess',
                'zamčený' => 'LockedMappableEntityAccess',
            ],
        ],
        ObjectMetadata::PLATFORM => [

            3 => ['zamknutá' => 'uzamčená'], //platformAccess
        ],
        ObjectMetadata::ELEVATOR => [

            8 => ['ploché' => null], //elevatorControl1ReliefMarking
            21 => ['ploché' => null, 'digitální' => null], //elevatorContro22ReliefMarking
            2 => [ //elevatorAccess
                'volně přístupný' => 'FreelyaccessibleMappableEntityAccess',
                'zamčený' => 'LockedMappableEntityAccess',
                'uzamčený' => 'LockedMappableEntityAccess',
            ],
            40 => [ //door1Opening
                'mechanické VEN' => 'MechanicalDoorOpening',
                'mechanické DOVNITŘ' => 'MechanicalDoorOpening',
            ],
            42 => [ //door2Opening
                'mechanické VEN' => 'MechanicalDoorOpening',
                'mechanické DOVNITŘ' => 'MechanicalDoorOpening',
            ],
        ],
        ObjectMetadata::WC => [

            55 => ['a' => true, 'n' => false], //washbasinIsHandle
            20 => ['a' => true, 'n' => false], //wcBasinIsPaperReach
            25 => ['a' => true, 'n' => false], //wcIsAlarmbutton
            22 => ['a' => true, 'n' => false], //wcIsChangingdesk
            28 => ['a' => true, 'n' => false], //wcIsRegularWC
            29 => ['a' => true, 'n' => false], //wcIsRegularWCBrailleMarking
            51 => ['a' => true, 'n' => false], //doorIsMarking
            4 => [ //wcCabinAccess
                'volně přístupná' => 'FreelyaccessibleMappableEntityAccess',
                'zamknutá' => 'LockedMappableEntityAccess',
            ],
            43 => ['chybí' => null], //handle1Type
            46 => ['chybí' => null], //handle2Type
        ],
        ObjectMetadata::RAMP_SKIDS => [
            27 => [
                'chybí' => false,
                'oboustranné' => true,
                'jednostranné' => true,
            ],
            2 => [ //rampLocalization
                'před hlavním vstupem' => 'EntranceRampSkidsLocalization',
                'před vedlejším vstupem' => 'EntranceRampSkidsLocalization',
            ],
            31 => [ //skidsLocalization
                'před hlavním vstupem' => 'EntranceRampSkidsLocalization',
                'před vedlejším vstupem' => 'EntranceRampSkidsLocalization',
            ],

        ],
    ];

    /**
     * Na vstupu dostane data z DPA
     *  zmeni kodovani,
     *  precisluje sloupce,
     *  upravi hodnoty na spravny dat. typ,
     *  sestavi CSV v internim formatu.
     *
     * @param string $data
     *
     * @return string
     */
    public function transform($data)
    {
        ini_set('memory_limit', '512M');

        $tranformedData = [];
        $data = iconv('CP1250', 'UTF-8', $data);

        $tmpFile = TEMP_DIR . '/dpa-import.csv';
        file_put_contents($tmpFile, $data);

        /** @var $lexer Lexer */
        /** @var $interpreter Interpreter */
        list($lexer, $interpreter) = $this->getCsvInterpreterLexer();

        $interpreter->addObserver(function (array $columns) use (&$tranformedData) {
            $tranformedData[] = $this->formatCsvLine($columns);
        });

        $lexer->parse($tmpFile, $interpreter);

        $exporter = $this->getCsvExporter();

        $tmpFile = TEMP_DIR . '/dpa-export.csv';
        $exporter->export($tmpFile, $tranformedData);

        return file_get_contents($tmpFile);
    }

    /**
     * Pripravi Interpreter a Lexer pro import dat z CSV v DPA formatu
     *
     * @return array
     */
    private function getCsvInterpreterLexer()
    {
        $csvConfig = new LexerConfig();
        $csvConfig->setDelimiter(';');

        $lexer = new Lexer($csvConfig);
        $interpreter = new Interpreter();

        return [$lexer, $interpreter];
    }

    /**
     * Pripravi Exporter pro export dat do interniho CSV formatu
     *
     * @return Exporter
     */
    private function getCsvExporter()
    {
        $config = new ExporterConfig();
        $config->setDelimiter(';');

        $ret = new Exporter($config);

        return $ret;
    }

    /**
     * Upravi poradi sloupcu a upravi data na pozadovane dat. typy
     *
     * @param array $columns
     *
     * @return array
     */
    private function formatCsvLine($columns)
    {
        $ret = [];

        foreach (self::$map['main'] as $dpaKey => $internalKey) {
            $value = trim($columns[$dpaKey - 1]);
            $this->prepareValue($value, $internalKey, 'main');
            $ret[$internalKey - 1] = $value;
        }

        $csvRelations = CsvResolver::getRelations();

        foreach (self::$relations as $relation => $startCols) {
            $i = 0;

            foreach ($startCols as $startCol) {
                $relationOffset = $csvRelations[$relation][$i];

                foreach (self::$map[$relation] as $dpaKey => $internalKey) {
                    if (isset($columns[$startCol + $dpaKey - 2])) {
                        $value = trim($columns[$startCol + $dpaKey - 2]);
                        $this->prepareValue($value, $internalKey, $relation);
                    } else {
                        $value = null;
                    }

                    $ret[$internalKey + $relationOffset - 2] = $value;
                }

                $i++;
            }
        }

        $this->addDescription($ret, $columns);
        $this->fixValues($ret);

        $mask = array_fill(0, 1060, null); // 1060 je pocet sloupcu dle aktualniho poctu priloh dle CsvResolver
        $ret += $mask;
        ksort($ret);

        return $ret;
    }

    /**
     * Nahrazeni hodnot do interniho formatu
     *
     * @param string $value
     * @param integer $internalKey
     * @param string $relation
     */
    private function prepareValue(&$value, $internalKey, $relation)
    {
        // hodnoty typu boolean (s vyjimkou sloupcu hallway1DoorMarking a hallway2DoorMarking - tam se jedna o ciselnik)
        if (ObjectMetadata::WC !== $relation || !in_array($internalKey, [38, 42])) {
            if ('ano' === $value) {
                $value = 1;
            } else if ('ne' === $value) {
                $value = 0;
            }
        }

        if (isset(self::$convertValues[$relation][$internalKey])) {
            foreach (self::$convertValues[$relation][$internalKey] as $original => $internal) {
                if ((string)$original === $value) {
                    $value = $internal;
                    break;
                }
            }
        }
    }

    /**
     * Moznost jeste upravit sestaveny radek v internim formatu.
     *
     * Klicem je index ve vyslednem poli, tzn. index v mapovani CsvResolveru - 1.
     *
     * @param array $ret
     */
    private function fixValues(&$ret)
    {
        /**
         * rozparsovani cisla domu, sloupce: - 24, 25, 26; mozne tvary
         * 1207/6b
         * 6b
         * 1207/IV (IV zahodim)
         */
        if (!empty($ret[24]) && (Strings::contains($ret['24'], '/') || Strings::match($ret[24], '~^(\d+)([a-zA-Z]+)$~'))) {
            if ($match = Strings::match($ret[24], '~^(\d+)/(\d+)([a-zA-Z]*)$~')) {
                $ret[24] = $match[1];
                $ret[25] = $match[2];
                $ret[26] = $match[3];
            } else if ($match = Strings::match($ret[24], '~^(\d+)([a-zA-Z]+)$~')) {
                $ret[24] = null;
                $ret[25] = $match[1];
                $ret[26] = $match[2];
            } else if ($match = Strings::match($ret[24], '~^(\d+)/([a-zA-Z]+)$~')) {
                $ret[24] = $match[1];
            }
        }

        // konverze data mapovani
        if (!empty($ret[12])) {
            $mappingDate = DateTime::createFromFormat('Ymd', $ret[12]);

            $ret[12] =  $mappingDate ? $mappingDate->format(DateTime::RFC3339) : $ret[12];
        }

        // konverze data modifikace
        if (!empty($ret[13])) {
            $mappingDate = DateTime::createFromFormat('Ymd', $ret[13]);

            $ret[13] =  $mappingDate ? $mappingDate->format(DateTime::RFC3339) : $ret[13];
        }

        // entrance1IsReservedParking a entrance2IsReservedParking urcit na zaklade entrance1NumberOfReservedParking, resp. entrance2NumberOfReservedParking
        if (empty($ret[28]) && !empty($ret[29])) {
            $ret[28] = 1;
        }

        if (empty($ret[80]) && !empty($ret[81])) {
            $ret[80] = 1;
        }
    }

    /**
     * Pokusi se dohledat textove popisy z externich souboru
     * @param array $ret vysledny radek v internim formatu
     * @param array $columns zdrojovy radek v DPA formatu
     */
    private function addDescription(&$ret, $columns)
    {
        // v CSV z DPA je v 1. sloupci interni ID, ktere neimportujeme, ale urcuje nazev TXT prilohy
        if (empty($ret[7])) {
            $dpaId = $columns[0];

            if ($dpaId) {
                foreach(Finder::findFiles("{$dpaId}*.txt")->in(STORAGE_DIR . "/dpa-texts") as $filename => $file) {
                    $description = @file_get_contents($filename);

                    if ($description) {
                        $ret[7] = iconv("CP1250", "UTF-8", $description);
                        break;
                    }
                }
            }
        }
    }
}
