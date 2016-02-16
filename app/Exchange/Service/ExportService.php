<?php

namespace MP\Exchange\Service;

use MP\Exchange\Export\ExporterFactory;
use MP\Manager\Resolver\IEnumValueResolver;
use MP\Util\Strings;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;

/**
 * Sluzba pro export mapovych objektu do podporovanych formatu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ExportService
{
    /** @var ExporterFactory */
    protected $expoterFactory;

    /** @var ExchangeMetadata */
    protected $metadata;

    /**
     * @param ExporterFactory $expoterFactory
     * @param ExchangeMetadata $metadata
     */
    public function __construct(ExporterFactory $expoterFactory, ExchangeMetadata $metadata)
    {
        $this->expoterFactory = $expoterFactory;
        $this->metadata = $metadata;
    }

    /**
     * @param array $objects
     * @param array $source
     *
     * @return string
     */
    public function export(array $objects, $source)
    {
        $expoter = $this->expoterFactory->create($source);

        $objects = $this->prepareObjects($objects);

        $data = $expoter->export($objects);

        return $data;
    }

    /**
     * Pripravi objekty pro export.
     *
     * @param array $objects
     *
     * @return array
     */
    protected function prepareObjects(array $objects)
    {
        $preparedObjects = [];

        foreach ($objects as &$object) {
            $preparedObjects[] = $this->prepareObject($object);
        }

        return $preparedObjects;
    }

    /**
     * @param ArrayHash|array $object
     * @param string|null $namespace
     *
     * @return array
     */
    protected function prepareObject($object, $namespace = null)
    {
        $preparedObject = [];

        foreach ($object as $key => $value) {
            if (isset($value)) {
                if (!Strings::endsWith($key, IEnumValueResolver::KEY_SUFFIX)) {
                    $preparedKey = Strings::toCamelCase($key);

                    if ($this->metadata->getRule($namespace, $preparedKey)) {
                        if (!is_array($value) && false === ($object instanceof \Traversable)) {
                            if ($value instanceof \DateTime) {
                                $preparedObject[$preparedKey] = $value->format(DateTime::RFC3339);
                            } else {
                                $preparedObject[$preparedKey] = $value;
                            }
                        } else {
                            foreach ($value as $item) {
                                $preparedObject[$preparedKey][] = $this->prepareObject($item, $key);
                            }
                        }
                    }
                } else {
                    $keyBaseName = Strings::substring($key, 0, Strings::length(-IEnumValueResolver::KEY_SUFFIX));

                    if (isset($object[$keyBaseName])) {
                        $preparedKey = Strings::toCamelCase($keyBaseName);

                        if ($this->metadata->getRule($namespace, $preparedKey)) {
                            $preparedObject[$preparedKey] = $value;
                        }
                    }
                }
            }
        }

        return $preparedObject;
    }
}
