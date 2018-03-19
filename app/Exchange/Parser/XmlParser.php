<?php

namespace MP\Exchange\Parser;

use MP\Util\Arrays;
use Nette\Utils\Strings;
use Sabre\Xml\Element\Base;
use Sabre\Xml\Reader;

/**
 * Parser XML importniho souboru podle XSD http://mapybezbarier.cz/XMLSchema.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class XmlParser implements IParser
{
    /** @const Podporovany namespace XML */
    const XMLNS = "http://mapybezbarier.cz/XMLSchema";

    /**
     * @param mixed $data
     * @return array
     */
    public function parse($data)
    {
        $parsedData = [];

        if (!empty($data)) {
            $namespace = $this->getSupportedNamespace();

            $xmlData = $this->parseXmlData($data, $namespace);

            $root = Arrays::get($xmlData, 'name', null);

            $this->checkNamespace($root, $namespace);

            $objects = Arrays::get($xmlData, 'value', []);

            foreach ($objects as $object) {
                $parsedData[] = $this->extractObjectData(Arrays::get($object, 'value', []), $namespace);
            }
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
     * Naparsuje vstupni XML.
     *
     * @param mixed $data
     * @param string $namespace
     * @return array
     */
    protected function parseXmlData($data, $namespace)
    {
        $reader = new Reader();
        $reader->elementMap = [
            "{$namespace}object" => Base::class,
        ];
        $reader->xml($data);

        try {
            $xmlData = $reader->parse();
        } catch (\Sabre\Xml\LibXMLException $e) {
            throw new \MP\Exchange\Exception\ParseException($e->getMessage());
        }

        return $xmlData;
    }

    /**
     * Extrahuje data objektu.
     *
     * @param array $object
     * @param string $namespace
     * @return array
     */
    protected function extractObjectData(array $object, $namespace)
    {
        $objectData = [];

        foreach ($object as $data) {
            $key = $this->extractDataKey($data);
            $value = $this->extractDataValue($data);

            if (is_array($value)) {
                $objectData[$key][] = $this->extractObjectData($value, $namespace);
            } else {
                $objectData[$key] = $value;
            }
        }

        return $objectData;
    }

    /**
     * Extrahuje klic data z namespace.
     *
     * @param array $data
     * @return string
     */
    protected function extractDataKey(array $data)
    {
        $namespace = $this->getSupportedNamespace();

        $key = Arrays::get($data, 'name', null);

        $this->checkNamespace($key, $namespace);

        $key = Strings::substring($key, Strings::length($namespace));

        return $key;
    }

    /**
     * Extrahuje klic data z namespace.
     *
     * @param array $data
     * @return string
     */
    protected function extractDataValue(array $data)
    {
        $value = Arrays::get($data, 'value', null);

        if (null !== $value) {
            if ('true' === $value) {
                $value = true;
            } else if ('false' === $value) {
                $value = false;
            }
        }

        return $value;
    }

    /**
     * Overi, zda hodnota nalezi do namespace.
     *
     * @param string $value
     * @param string $namespace
     */
    protected function checkNamespace($value, $namespace)
    {
        if (!Strings::contains($value, $namespace)) {
            throw new \MP\Exchange\Exception\ParseException("Invalid namespace for value '{$value}'.");
        }
    }

    /**
     * Vrati namespace XML.
     *
     * @return string
     */
    protected function getSupportedNamespace()
    {
        return '{' . self::XMLNS . '}';
    }
}
