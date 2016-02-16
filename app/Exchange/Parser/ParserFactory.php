<?php

namespace MP\Exchange\Parser;

use Nette\DI\Container;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ParserFactory
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
     * @return IParser
     */
    public function create($source)
    {
        if (!isset($this->mapping[$source['format']])) {
            throw new \Nette\InvalidArgumentException("Parser service name not specified for type '{$source['format']}'.");
        }

        $parser = $this->context->getService($this->mapping[$source['format']]);

        return $parser;
    }
}
