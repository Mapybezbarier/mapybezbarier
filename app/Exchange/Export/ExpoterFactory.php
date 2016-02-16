<?php

namespace MP\Exchange\Export;

use Nette\DI\Container;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ExporterFactory
{
    /** @var Container */
    protected $context;

    /** @var array */
    protected $mapping;

    /**
     * @param Container $context
     * @param array $mapping
     */
    public function __construct(Container $context, array $mapping)
    {
        $this->context = $context;
        $this->mapping = $mapping;
    }

    /**
     * @param array $source
     *
     * @return IExpoter
     */
    public function create($source)
    {
        if (!isset($this->mapping[$source['format']])) {
            throw new \Nette\InvalidArgumentException("Exporter service name not specified for type '{$source['format']}'.");
        }

        $expoter = $this->context->getService($this->mapping[$source['format']]);

        return $expoter;
    }
}
