<?php

namespace MP\Manager;

use MP\Manager\Resolver\IEnumValueResolver;
use MP\Mapper\DatabaseMapperFactory;
use MP\Util\Lang\Lang;
use Nette\Utils\Paginator;

/**
 * Predek manazeru s podporou importu.
 *
 * Abstraktniho manazera rozsiruje o porporu nacteni hodnot cizich klicu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractEnumManager extends AbstractManager
{
    /** @var IEnumValueResolver */
    protected $enumValueResolver;

    /**
     * @param DatabaseMapperFactory $mapperFactory
     * @param Lang $lang
     * @param IEnumValueResolver $enumValueResolver
     */
    public function __construct(DatabaseMapperFactory $mapperFactory, Lang $lang, IEnumValueResolver $enumValueResolver)
    {
        parent::__construct($mapperFactory, $lang);

        $this->enumValueResolver = $enumValueResolver;
    }

    /**
     * @override Pred ulozenim objektu donacte hodnoty cizich klicu.
     *
     * @param array $values
     * @return array
     */
    public function persist(array $values)
    {
        $values = $this->enumValueResolver->persistResolve($values);

        return parent::persist($values);
    }

    /**
     * @override Donacteni hodnot z ciselniku.
     *
     * @param array|null $restrictor
     * @param array|null $order
     * @param Paginator|null $paginator
     *
     * @return array
     */
    public function findAll($restrictor = null, $order = null, Paginator $paginator = null)
    {
        $objects = parent::findAll($restrictor, $order, $paginator);

        foreach ($objects as &$object) {
            $object = $this->enumValueResolver->findResolve($object);
        }

        return $objects;
    }

    /**
     * @override Donacteni hodnot z ciselniku.
     *
     * @param array $restrictor
     * @param array|null $order
     *
     * @return array|null
     */
    public function findOneBy(array $restrictor, $order = null)
    {
        $object = parent::findOneBy($restrictor, $order);

        if ($object) {
            $object = $this->enumValueResolver->findResolve($object);
        }

        return $object;
    }
}
