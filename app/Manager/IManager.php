<?php

namespace MP\Manager;
use Nette\Utils\Paginator;

/**
 * Rozhranni manazeru
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IManager
{
    /**
     * Dle restriktoru vyhleda jeden konkretni zaznam
     *
     * @param array $restrictor
     * @param array|null $order
     *
     * @return array|null
     */
    public function findOneBy(array $restrictor, $order = null);

    /**
     * Vyhleda zaznam podle ID.
     *
     * @param int $id
     *
     * @return array|null
     */
    public function findOneById($id);

    /**
     * Vraci zaznamy dle restriktoru
     *
     * @param array|null $restrictor
     * @param array|null $order
     * @param Paginator|null $paginator
     *
     * @return array
     */
    public function findAll($restrictor = null, $order = null, Paginator $paginator = null);

    /**
     * Vraci pocet zaznamu dle restriktoru
     *
     * @param array|null $restrictor
     *
     * @return int
     */
    public function findCount($restrictor = null);

    /**
     * Vrati prvni zaznam dle restriktoru.
     *
     * @param array|null $order
     *
     * @return array|null
     */
    public function findFirst($order = null);

    /**
     * Ulozi zaznam (provede update/insert v zavislosti na tom, jestli ma entita nastaven primarni klic)
     *
     * @param array $values
     * @return array
     */
    public function persist(array $values);

    /**
     * Smaze zaznam podle ID.
     *
     * @param int $id
     */
    public function remove($id);

    /**
     * Smaze zaznam podle restriktoru.
     *
     * @param array $restrictor
     */
    public function removeBy(array $restrictor);
}
