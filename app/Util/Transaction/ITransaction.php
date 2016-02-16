<?php


namespace MP\Util\Transaction;

/**
 * Rozhranni transakcniho wrapperu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface ITransaction
{
    /** @const Priznak prikazu SELECT. */
    const SELECT_FOR_UPDATE_ON = true,
        SELECT_FOR_UPDATE_OFF = false;

    /**
     * Zahraji transakce.
     *
     * @param string|null $savePoint
     */
    public function begin($savePoint = null);

    /**
     * Commit transakce.
     *
     * @param string|null $savePoint
     */
    public function commit($savePoint = null);

    /**
     * Rollback transakce.
     *
     * @param string|null $savePoint
     */
    public function rollback($savePoint = null);

    /**
     * Vraci true, pokud je aktualne vykonavany kod v transkaci.
     *
     * @return bool
     */
    public function isRunning();

    /**
     * Zamykat vsechny operace select az do odovlani pro update.
     *
     * @param bool $on
     */
    public function setSelectForUpdate($on);

    /**
     * Zamykaji selecty radky pro update.
     *
     * @return bool
     */
    public function isSelectForUpdate();
}
