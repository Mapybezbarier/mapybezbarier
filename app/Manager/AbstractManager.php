<?php

namespace MP\Manager;

use MP\Mapper\DatabaseMapperFactory;
use MP\Mapper\IMapper;
use MP\Util\Arrays;
use MP\Util\Lang\Lang;
use Nette\Utils\Paginator;

/**
 * Abstraktni predek manazeru.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractManager implements IManager
{
    /** @var DatabaseMapperFactory */
    protected $mapperFactory;

    /** @var IMapper */
    protected $mapper;

    /** @var Lang */
    protected $lang;

    /** @var string */
    protected $table;

    /**
     * @param DatabaseMapperFactory $mapperFactory
     * @param Lang $lang
     */
    public function __construct(DatabaseMapperFactory $mapperFactory, Lang $lang)
    {
        $this->mapperFactory = $mapperFactory;
        $this->lang = $lang;
    }

    /**
     * Inicializace tridy. Metoda volana v ramci sestaveni kontejneru na zaklade setupu v ManagerExtension.
     */
    public function init()
    {
        if (null === $this->mapper) {
            if (null === $this->table) {
                throw new \Nette\InvalidStateException("Manager '" . static::class . "' does not specify its table.");
            }

            $this->mapper = $this->mapperFactory->create(static::class, $this->table);
        }
    }

    /**
     * @param array|null $restrictor
     * @param array|string|null $order
     * @param Paginator|null $paginator
     *
     * @return array
     */
    public function findAll($restrictor = null, $order = null, Paginator $paginator = null)
    {
        if (null !== $paginator) {
            $limit = $paginator->getItemsPerPage();
            $offset = $paginator->getOffset();
        } else {
            $limit = $offset = null;
        }

        return $this->mapper->selectAll($restrictor, $order, $limit, $offset) ?: [];
    }

    /**
     * @param array $restrictor
     * @param array|null $order
     *
     * @return array|null
     */
    public function findOneBy(array $restrictor, $order = null)
    {
        return $this->mapper->selectOne($restrictor, $order);
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    public function findOneById($id)
    {
        $restrictor = [["[" . IMapper::ID ."] = %i", $id]];

        return $this->findOneBy($restrictor);
    }

    /**
     * @param array|null $order
     *
     * @return array
     */
    public function findFirst($order = null)
    {
        return $this->findOneBy([], $order);
    }

    /**
     * @param array|null $restrictor
     *
     * @return int
     */
    public function findCount($restrictor = null)
    {
        return $this->mapper->count($restrictor);
    }

    /**
     * @param array $values
     * @return array
     */
    public function persist(array $values)
    {
        $id = Arrays::get($values, IMapper::ID, null);

        if (null === $id) {
            $values[IMapper::ID] = $this->mapper->insert($values);
        } else {
            unset($values[IMapper::ID]);

            $this->mapper->update($values, [["[" . IMapper::ID. "] = %i", $id]]);

            $values[IMapper::ID] = $id;
        }

        return $values;
    }

    /**
     * @param int $id
     */
    public function remove($id)
    {
        $this->mapper->delete([["[" . IMapper::ID. "] = %i", $id]]);
    }

    /**
     * @param array $restrictor
     */
    public function removeBy(array $restrictor)
    {
        $this->mapper->delete($restrictor);
    }

    /**
     * Vrati nazev spravovane databazove tabulky.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Nastavi nazev spravovane databazove tabulky.
     *
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
}
