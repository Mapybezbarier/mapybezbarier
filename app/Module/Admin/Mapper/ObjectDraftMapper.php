<?php

namespace MP\Module\Admin\Mapper;

use MP\Mapper\DatabaseMapper;

/**
 * Databazovy mapper pro drafty mapovych objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectDraftMapper extends DatabaseMapper
{
    /**
     * @override Nastaveni title
     *
     * @param string[] $query
     * @return array
     */
    protected function buildSelect(&$query)
    {
        $query = ["SELECT *, COALESCE ([pair_key], (
            SELECT [title]
            FROM [map_object]
            LEFT JOIN (
                SELECT DISTINCT ON ([map_object_id]) *
                FROM [map_object_lang]
                ORDER BY [map_object_id], [lang_id] = %s", $this->lang->getLang(), " DESC
            ) [lang] ON ([map_object].[id] = [lang].[map_object_id])
            WHERE [map_object].[object_id] = [{$this->table}].[map_object_object_id]
        )) AS [title] FROM %n", $this->table];

        return $query;
    }
}
