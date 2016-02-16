<?php

namespace MP\Module\Web\Component;

use MP\Manager\ExchangeSourceManager;
use Nette\DI\Container;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class RendererFactory implements IRendererFactory
{
    /** @const Nazev klice objektu, pod kterym se dohledava renderer */
    const SOURCE = 'source_id';

    /** @var Container */
    protected $context;

    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var IRendererFactory */
    protected $defaultRendererFactory;

    /** @var array */
    protected $mapping = [];

    /** @var IRenderer[] */
    protected $renderers = [];

    /**
     * @param Container $context
     * @param ExchangeSourceManager $sourceManager
     * @param IRendererFactory $defaultRendererFactory
     * @param array $mapping
     */
    public function __construct(
        Container $context,
        ExchangeSourceManager $sourceManager,
        IRendererFactory $defaultRendererFactory,
        array $mapping)
    {
        $this->context = $context;
        $this->sourceManager = $sourceManager;
        $this->defaultRendererFactory = $defaultRendererFactory;
        $this->mapping = $mapping;
    }

    /**
     * @param array $object
     *
     * @return IRenderer
     */
    public function create(array $object)
    {
        if (!empty($object[self::SOURCE])) {
            if (!isset($this->renderers[$object[self::SOURCE]])) {
                $this->renderers[$object[self::SOURCE]] = $this->getInstance($object);
            }

            $renderer = $this->renderers[$object[self::SOURCE]];

            return $renderer;
        } else {
            throw new \Nette\InvalidArgumentException("Cannot render object of unknown source");
        }
    }

    /**
     * @param array $object
     *
     * @return IRenderer
     */
    protected function getInstance(array $object)
    {
        $source = $this->sourceManager->findOneById($object[self::SOURCE]);

        if ($source) {
            if (isset($this->mapping[$source['format']])) {
                $factory = $this->context->getService($this->mapping[$source['format']]);
            } else {
                $factory = $this->defaultRendererFactory;
            }

            $renderer = $factory->create($object);

            return $renderer;
        } else {
            throw new \Nette\InvalidArgumentException("Cannot render object of unknown source");
        }
    }
}
