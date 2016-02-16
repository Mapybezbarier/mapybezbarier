<?php

namespace MP\Module\SourceDetail;

use MP\Manager\ExchangeSourceManager;
use Nette\DI\Container;

/**
 * Tovarna na sestavovace dat detailu dle typu zdroje.
 */
class SourceDetailFactory
{
    /** @const Nazev klice objektu, pod kterym se dohledava renderer */
    const SOURCE = 'source_id';

    /** @var Container */
    protected $context;

    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var array */
    protected $mapping;

    /** @var array */
    protected $services = [];

    /**
     * @param Container $context
     * @param ExchangeSourceManager $sourceManager
     * @param array $mapping
     */
    public function __construct(Container $context, ExchangeSourceManager $sourceManager, array $mapping)
    {
        $this->context = $context;
        $this->sourceManager = $sourceManager;
        $this->mapping = $mapping;
    }

    /**
     * @param array $object
     *
     * @return ISourceDetail|null
     */
    public function create(array $object)
    {
        if (!empty($object[self::SOURCE])) {
            if (!isset($this->services[$object[self::SOURCE]])) {
                $this->services[$object[self::SOURCE]] = $this->createBySource($object[self::SOURCE]);
            }

            $service = $this->services[$object[self::SOURCE]];

            return $service;
        } else {
            throw new \Nette\InvalidArgumentException("Cannot prepare detail of object of unknown source");
        }
    }

    /**
     * @param int $source
     *
     * @return ISourceDetail
     */
    protected function createBySource($source)
    {
        $service = null;

        $source = $this->sourceManager->findOneById($source);

        if ($source) {
            if (isset($this->mapping[$source['format']])) {
                $service = $this->context->getService($this->mapping[$source['format']]);
            }

            return $service;
        } else {
            throw new \Nette\InvalidArgumentException("Cannot prepare detail of object of unknown source");
        }
    }
}
