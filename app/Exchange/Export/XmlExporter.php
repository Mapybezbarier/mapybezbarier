<?php

namespace MP\Exchange\Export;

use Nette\Utils\Validators;
use Sabre\Xml\Writer;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class XmlExporter implements IExpoter
{
    /** @const Podporovany namespace XML */
    const XMLNS = "http://mapybezbarier.cz/XMLSchema";

    /** @const Verze XML dokumentu. */
    const VERSION = '1.0';

    /** @const Kodovani XML dokumentu. */
    const ENCODING = 'UTF-8';

    /** @const Kodovani XML dokumentu. */
    const STANDALONE = 'yes';

    /** @const Vyznamne elementy vystupniho XML */
    const ELEMENT_ROOT = 'objects',
        ELEMENT_OBJECT = 'object';

    /**
     * @param array $data
     * @return string
     */
    public function export(array $data)
    {
        $writer = new Writer();
        $writer->openMemory();
        $writer->startDocument(self::VERSION, self::ENCODING, self::STANDALONE);
        $writer->namespaceMap = [
            self::XMLNS => null,
        ];

        try {
            $writer->startElement('{' . self::XMLNS . '}' . self::ELEMENT_ROOT);

            foreach ($data as $object) {
                $this->writeObjectData($writer, self::ELEMENT_OBJECT, $object);
            }

            $writer->endElement();
        } catch (\Sabre\Xml\LibXMLException $e) {
            throw new \MP\Exchange\Exception\ExportException($e->getMessage());
        }

        $xml = $writer->outputMemory();

        return $xml;
    }

    /**
     * Zapise data objektu.
     *
     * @param Writer $writer
     * @param string $element
     * @param array $data
     */
    protected function writeObjectData(Writer $writer, $element, array $data)
    {
        $writer->startElement($element);

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $writer->startElement($key);
                $writer->write($this->prepareValue($value));
                $writer->endElement();
            } else {
                foreach ($value as $values) {
                    $this->writeObjectData($writer, $key, $values);
                }
            }
        }

        $writer->endElement();
    }

    /**
     * Pripravi hodnotu pro vypis.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function prepareValue($value)
    {
        if (Validators::is($value, 'boolean')) {
            $value = ($value ? 'true' : 'false');
        }

        return $value;
    }
}
