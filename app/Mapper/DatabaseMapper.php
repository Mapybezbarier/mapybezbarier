<?php

namespace MP\Mapper;

use Dibi\Connection;
use MP\Util\Lang\Lang;
use MP\Util\Transaction\DibiTransaction;

/**
 * Vychozi databazovy mapper.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DatabaseMapper implements IMapper
{
    /** @var Context */
    protected $context;

    /** @var Connection */
    protected $connection;

    /** @var DibiTransaction */
    protected $transaction;

    /** @var Lang */
    protected $lang;

    /** @var string */
    protected $table;

    /**
     * @param Context $context
     * @param Connection $connection
     * @param DibiTransaction $transaction
     * @param Lang $lang
     */
    public function __construct(Context $context, Connection $connection, DibiTransaction $transaction, Lang $lang)
    {
        $this->context = $context;
        $this->connection = $connection;
        $this->transaction = $transaction;
        $this->lang = $lang;
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

    /**
     * @param array|null $restrictor
     * @param array|null $order
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array|null
     * @throws \Dibi\Exception
     */
    public function selectAll($restrictor = null, $order = null, $limit = null, $offset = null)
    {
        $result = [];

        $query = $this->buildSelectAllQuery($restrictor, $order, $limit, $offset);

        $rows = $this->executeQuery($query)->fetchAll();

        if ($rows) {
            foreach ($rows as $row) {
                $result[] = $row->toArray();
            }
        }

        return $result;
    }

    /**
     * @param array $restrictor
     * @param array|null $order
     *
     * @return array|null
     * @throws \Dibi\Exception
     */
    public function selectOne(array $restrictor, $order = null)
    {
        $result = null;

        $query = $this->buildSelectAllQuery($restrictor, $order);
        $query = array_merge($query, ["%lmt", 1]);

        $row = $this->executeQuery($query)->fetch();

        if ($row) {
            $result = $row->toArray();
        }

        return $result;
    }

    /**
     * @param array $values
     * @return int
     */
    public function insert(array $values)
    {
        if (!isset($values[IMapper::ID])) {
            $values[IMapper::ID] = $this->getNextId();
        }

        $query = ["INSERT INTO %n", $this->table, $values];

        $this->executeQuery($query);

        return $values[IMapper::ID];
    }

    /**
     * @param array $values
     * @param array $restrictor
     * @param string|null $returning
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    public function update(array $values, array $restrictor, $returning = null)
    {
        $query = ["UPDATE %n", $this->table, "SET", $values];
        $query = $this->buildWhere($restrictor, $query);

        if (null !== $returning) {
            $query[] = " RETURNING %n";
            $query[] = $returning;
        }

        $result = $this->executeQuery($query);

        return $result;
    }

    /**
     * @param array $restrictor
     * @return \Dibi\Result|int
     */
    public function delete(array $restrictor)
    {
        $query = ["DELETE FROM %n", $this->table];
        $query = $this->buildWhere($restrictor, $query);

        $result = $this->executeQuery($query);

        return $result;
    }

    /**
     * @param array|null $restrictor
     *
     * @return int|null
     * @throws \Dibi\Exception
     */
    public function count($restrictor)
    {
        $result = null;

        $query = ["SELECT COUNT(*) FROM %n", $this->table];

        $this->buildWhere($restrictor, $query);

        $result = $this->executeQuery($query)->fetchSingle();

        return $result;
    }

    /**
     * @param array|null $restrictor
     * @param array|null $order
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array|\string[]
     */
    protected function buildSelectAllQuery($restrictor = null, $order = null, $limit = null, $offset = null)
    {
        $this->buildSelect($query);
        $this->buildWhere($restrictor, $query);
        $this->buildOrder($order, $query);
        $this->buildLimitOffset($limit, $offset, $query);

        return $query;
    }

    /**
     * Sestavi SELECT cast dostazu.
     *
     * @param string[] $query
     * @return array
     */
    protected function buildSelect(&$query)
    {
        $query = ["SELECT * FROM %n", $this->table];

        return $query;
    }

    /**
     * Sestavu WHERE cast dotazu
     *
     * @param array|null $restrictor
     * @param string[] $query aktualne sestavena cast query, ke ktere je pripojena WHERE cast dotazu
     *
     * @return string[] query
     */
    protected function buildWhere($restrictor = null, &$query)
    {
        if (null !== $restrictor) {
            $query[] = "WHERE %and";
            $query[] = $restrictor;
        }

        return $query;
    }

    /**
     * Sestavi ORDER BY cast dotazu
     *
     * @param array|null $order
     * @param string[] $query
     *
     * @return string[]
     */
    protected function buildOrder($order, &$query)
    {
        if (null === $order) {
            $order = [];
        }

        if (is_array($order)) {
            $order = array_merge($order, [IMapper::ID => IMapper::ORDER_ASC]);

            $query = array_merge($query, ["ORDER BY %by", $order]);
        }

        return $query;
    }

    /**
     * Sestavi LIMIT a OFFSET cast dotazu.
     *
     * @param int|null $limit
     * @param int|null $offset
     * @param string[] $query
     *
     * @return string[]
     */
    protected function buildLimitOffset($limit, $offset, &$query)
    {
        if (null !== $limit) {
            $query = array_merge($query, ["%lmt", (int)$limit]);
        }

        if (null !== $offset) {
            $query = array_merge($query, ["%ofs", (int)$offset]);
        }

        return $query;
    }

    /**
     * @param array|string $query
     *
     * @param bool $rollbackOnError
     * @return \Dibi\Result|int
     * @throws \Dibi\Exception
     */
    protected function executeQuery($query, $rollbackOnError = true)
    {
        try {
            $result = $this->connection->query($query);
        } catch (\Dibi\Exception $e) {
            if ($rollbackOnError && $this->transaction->isRunning()) {
                $this->transaction->rollback();
            }

            throw $e;
        }

        return $result;
    }

    /**
     * Vraci dalsi ID ze sekcence pro tabulku mapperu.
     */
    protected function getNextId()
    {
        $id = null;

        $query = ["SELECT nextval(%s)", $this->getSequenceName()];

        $result = $this->executeQuery($query);

        if ($result) {
            $id = $result->fetchSingle();
        }

        if (empty($id) || false === is_numeric($id)) {
            throw new \Nette\UnexpectedValueException("Sequence generation returned non-numeric value.");
        }

        return $id;
    }

    /**
     * Dle nazvu mapperem spravovane tabulky vraci nazev sekvence pro IDcka.
     *
     * @return string
     */
    protected function getSequenceName()
    {
        return "{$this->table}_id_seq";
    }
}
