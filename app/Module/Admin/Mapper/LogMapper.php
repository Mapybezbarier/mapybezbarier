<?php

namespace MP\Module\Admin\Mapper;

use MP\Mapper\DatabaseMapper;

/**
 * Databazovy mapper pro log akci uzivatelu.
 *
 */
class LogMapper extends DatabaseMapper
{
    /**
     * @override prijoinuje uzivatele a prida jeho login
     *
     * @param string[] $query
     * @return array
     */
    protected function buildSelect(&$query)
    {
        $query = ["SELECT [l].*, [u].[login] FROM %n [l] JOIN %n [u] ON ([l].[user_id] = [u].[id])", $this->table, 'user'];

        return $query;
    }
}
