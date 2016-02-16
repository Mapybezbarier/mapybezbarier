<?php

namespace MP\Exchange\Parser;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use MP\Exchange\Export\CsvResolver\CsvResolver;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class CsvParser implements IParser
{
    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $parsedData = [];

        if (!empty($data)) {
            /** @var $lexer Lexer */
            /** @var $interpreter Interpreter */
            list($lexer, $interpreter) = $this->getCsvInterpreterLexer();

            $interpreter->addObserver(function(array $columns) use(&$parsedData) {
                $parsedData[] = $this->parseCsvLine($columns);
            });

            $tmpFile = TEMP_DIR . '/import.csv';
            file_put_contents($tmpFile, $data);

            $lexer->parse($tmpFile, $interpreter);
        }

        return $parsedData;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return IParser::TYPE_INTERNAL;
    }

    /**
     * Pripravi Interpreter a Lexer pro import dat z CSV v internim formatu
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
     * Zpracuje radek z CSV - rozdeli na jednoltive prilohy a namapuje
     * @param array $columns data z CSV s klici cislovanymi od 0
     * @return array objekt
     */
    protected function parseCsvLine($columns)
    {
        $ret = $this->parseMain($columns);

        foreach (CsvResolver::getRelations() as $key => $starts) {
            $ret[$key] = [];

            foreach ($starts as $start) {
                if ($relation = $this->parseRelation($key, $columns, $start)) {
                    $ret[$key][] = $relation;
                }
            }
        }

        return $ret;
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    protected function parseMain($columns)
    {
        $ret = [];

        foreach (CsvResolver::getColsMap() as $col => $key) {
            if ("" !== $columns[$col-1]) {
                $ret[$key] = $columns[$col-1];
            }
        }

        return $ret;
    }

    /**
     * @param string $key
     * @param array $columns
     * @param int $start
     *
     * @return array
     */
    protected function parseRelation($key, $columns, $start)
    {
        $ret = [];

        foreach (CsvResolver::getColsMap($key) as $col => $key) {
            if ("" !== $columns[$start + $col - 2]) {
                $ret[$key] = $columns[$start + $col - 2];
            }
        }

        return $ret;
    }
}
