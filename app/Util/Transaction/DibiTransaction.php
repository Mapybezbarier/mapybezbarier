<?php

namespace MP\Util\Transaction;

use Dibi\Connection;

/**
 * Wrapper nad Dibi connection. SQL BEGIN/COMMIT se provadi skrze driver a jen v pripade, prvniho/posledniho prikazu
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DibiTransaction implements ITransaction
{
    /** @var  Connection */
    protected $connection;

    /** @var int Uroven zanoreni transakce */
    protected $transactionCounter = 0;

    /** @var bool zamykat radky selectu pro update */
    protected $selectForUpdate = false;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string|null $savePoint
     * @throws \Dibi\Exception
     */
    public function begin($savePoint = null)
    {
        if (0 === $this->transactionCounter) {
            $this->connection->begin($savePoint);
        }

        $this->transactionCounter++;
    }

    /**
     * @param string|null $savePoint
     * @throws \Dibi\Exception
     * @throws \Nette\InvalidStateException
     */
    public function commit($savePoint = null)
    {
        $this->transactionCounter--;

        if (0 === $this->transactionCounter) {
            $this->connection->commit($savePoint);
            $this->setSelectForUpdate(ITransaction::SELECT_FOR_UPDATE_OFF);
        } elseif ($this->transactionCounter < 0) {
            throw new \Nette\InvalidStateException("Transaction was not started or there was more COMMITs than BEGINs.");
        }
    }

    /**
     * @param string|null $savePoint
     * @throws \Dibi\Exception
     * @throws \Nette\InvalidStateException
     */
    public function rollback($savePoint = null)
    {
        if ($this->isRunning()) {
            $this->connection->rollback($savePoint);
            $this->setSelectForUpdate(ITransaction::SELECT_FOR_UPDATE_OFF);
            $this->transactionCounter = 0;
        } else {
            throw new \Nette\InvalidStateException("Unable to rollback transaction - no transaction is currently running.");
        }
    }

    /**
     * Zamykat vsechny operace select az do odovlani pro update.
     *
     * @param bool $on
     */
    public function setSelectForUpdate($on)
    {
        $this->selectForUpdate = $on;
    }

    /**
     * Zamykaji selecty radky pro update.
     *
     * @return bool
     */
    public function isSelectForUpdate()
    {
        return $this->selectForUpdate == ITransaction::SELECT_FOR_UPDATE_ON;
    }

    /**
     * Vraci true, pokud je aktualne vykonavany kod v transkaci.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->transactionCounter > 0;
    }
}
