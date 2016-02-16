<?php

namespace MP\Exchange\Validator;

use Nette\DI\Container;

/**
 * Tovarna na validatory importovanych dat.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ValidatorsFactory
{
    /** @var Container */
    protected $context;

    /**
     * @param Container $context
     */
    public function __construct(Container $context)
    {
        $this->context = $context;
    }

    /**
     * Vrati validatory, ktere dokazi zvalidovat importovana data ze zdroje.
     *
     * @param array $source
     *
     * @return IValidator[]
     */
    public function create($source)
    {
        $validators = [];

        $serviceNames = $this->context->findByType(IValidator::class);

        foreach ($serviceNames as $serviceName) {
            /** @var IValidator $validator */
            $validator = $this->context->getService($serviceName);

            $format = $validator->getFormat();

            if (null === $format || $source['format'] === $format) {
                $validators[] = $validator;
            }
        }

        usort($validators, callback($this, 'sort'));

        return $validators;
    }

    /**
     * Prvni jdou validatory kvality, pote validatory konzistence. Pokud jsou validatory stejneho typu, prvni
     * jde ten obecnejsi.
     *
     * @param IValidator $a
     * @param IValidator $b
     *
     * @return int
     */
    public function sort(IValidator $a, IValidator $b)
    {
        if (IValidator::TYPE_QUALITY === $a->getType() && IValidator::TYPE_CONSISTENCY === $b->getType()) {
            $order = -1;
        } else if (IValidator::TYPE_CONSISTENCY === $a->getType() && IValidator::TYPE_QUALITY === $b->getType()) {
            $order = 1;
        } else {
            if (null === $a->getFormat() && null !== $b->getFormat()) {
                $order = -1;
            } else if (null !== $a->getFormat() && null === $b->getFormat()) {
                $order = 1;
            } else {
                $order = 0;
            }
        }

        return $order;
    }
}
