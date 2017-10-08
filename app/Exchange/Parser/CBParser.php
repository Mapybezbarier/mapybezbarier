<?php

namespace MP\Exchange\Parser;

use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use MP\Manager\ObjectTypeManager;
use MP\Object\ObjectMetadata;
use MP\Util\Address\Address;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Sabre\Xml\ParseException;


/**
 * Parser pro data z Ceskych Budejovic
 * Jedna se o data v externim formatu - format dat je CSV
 * @author Jakub Vrbas
 */
class CBParser implements IParser
{
    /** @var array mapa ciselnikoveho atributu - pristupnost */
    protected $mapAccessibility = [
        1 => ObjectMetadata::ACCESSIBILITY_OK,
        2 => ObjectMetadata::ACCESSIBILITY_PARTLY,
        3 => ObjectMetadata::ACCESSIBILITY_NO,
    ];

    /** @var array mapa ciselnikoveho atributu - typ objektu */
    protected $mapObjectType;

    /** @var ObjectTypeManager */
    protected $objectTypeManager;
    
    /**
     * @param ObjectTypeManager $objectTypeManager
     */
    public function __construct(ObjectTypeManager $objectTypeManager)
    {
        $this->objectTypeManager = $objectTypeManager;
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $ret = [];

        if (!empty($data)) {
            /** @var $lexer Lexer */
            /** @var $interpreter Interpreter */
            list($lexer, $interpreter) = $this->getCsvInterpreterLexer();

            $interpreter->addObserver(function(array $row) use(&$ret) {
                $ret[] = $this->prepareMapObject($row);
            });

            $tmpFile = TEMP_DIR . '/import-cb.csv';
            file_put_contents($tmpFile, $data);

            $lexer->parse($tmpFile, $interpreter);
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
     * Pripravi Interpreter a Lexer pro import dat z CSV
     * @return array
     */
    protected function getCsvInterpreterLexer()
    {
        $csvConfig = new LexerConfig();
        $csvConfig->setDelimiter(';');

        $lexer = new Lexer($csvConfig);
        $interpreter = new Interpreter();

        return [$lexer, $interpreter];
    }

    /**
     * Zpracuje 1 objekt
     * @param array $row
     * @return array
     */
    protected function prepareMapObject($row)
    {
        // kontrola formatu dat
        if (count($row) < 30) {
            throw new ParseException('CSV neni v predpokladanem formatu.');
        }

        $row['title'] = $row[1];

        $ret = [
            'title' => $row[1],
            'description' => $this->parseDescription($row[29]),
            'street' => $row[2],
            'city' => $row[4],
            'zipcode' => Strings::replace($row[5], '/\s/', ''),
            'longitude' => $row[6],
            'latitude' => $row[7],
            'webUrl' => $row[10],
            'accessibility' => Arrays::get($this->mapAccessibility, $row[11], ObjectMetadata::ACCESSIBILITY_NO),
            'objectType' => $this->parseObjectType($row[12]),
            'externalData' => $this->prepareExternalData($row),
            'mappingDate' => time(),
        ];

        Address::parseHouseNumber($ret, $row, 3);

        return $ret;
    }

    /**
     * Popis obsahuje HTML znacky a escapovane znaky s diakritikou
     * @param string $rawDescription
     * $return string
     */
    protected function parseDescription($rawDescription)
    {
        // nejprve pridam odradkovani pro nadpisy
        $ret = str_replace("\n<h3>", "\n\n<h3>", $rawDescription);
        return html_entity_decode(strip_tags($ret), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Dohleda typ objektu - zdroj pouziva stejne nazvoslovi jako interni struktury
     * @param string $rawObjectType
     * @return string
     */
    protected function parseObjectType($rawObjectType)
    {
        if (!$this->mapObjectType) {
            $this->mapObjectType = Arrays::pairs($this->objectTypeManager->findAll(), 'pair_key', 'title');
        }

        return Arrays::get($this->mapObjectType, $rawObjectType, null);
    }

    /**
     * @param array $row
     * @return array
     */
    protected function prepareExternalData($row)
    {
        $ret = [
            'id' => $row[0],
            'standard_pictograms' => [],
        ];

        for ($i = 13; $i <= 26; $i++) {
            $ret['standard_pictograms'][$i-13] = (bool)$row[$i];
        }

        try {
            $ret = Json::encode($ret);
        } catch (JsonException $e) {
            throw new ParseException('Nepodarilo se externi data zakodovat jako JSON.');
        }

        return $ret;
    }

}
