<?php

namespace MP\Mapper;

/**
 * Rozhranni mapperu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IMapper
{
    /** @const Nazev sloupce nesouci primarni klic */
    const ID = 'id';

    /** @const Nazev sloupce nesouci jazyk */
    const LANG = 'lang_id';

    /** @const Smer razeni */
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * @param array|null $restrictor
     * @param array|null $order
     * @param int|null $limit
     * @param int|null $offset
     */
    public function selectAll($restrictor = null, $order = null, $limit = null, $offset = null);

    /**
     * @param array $restrictor
     * @param array null $order
     */
    public function selectOne(array $restrictor, $order = null);

    /**
     * @param array $values
     * @retun int
     */
    public function insert(array $values);

    /**
     * @param array $values
     * @param array $restrictor
     * @param string|null $returning
     */
    public function update(array $values, array $restrictor, $returning = null);

    /**
     * @param array $restrictor
     */
    public function delete(array $restrictor);

    /**
     * @param array|null $restrictor
     *
     * @return int
     */
    public function count($restrictor);
}
