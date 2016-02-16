<?php

namespace MP\Mapper;

/**
 * Mapper pro frontu nezpracovanych adres.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class GeocodingMapper extends DatabaseMapper
{
    /**
     * Vrati zacatek fronty s prijoinovanymi informacemi o adrese objektu
     * @param bool $priorityOnly
     * @param int $batchLimit
     * @return \Dibi\Row[]
     */
    public function getQueue($priorityOnly, $batchLimit)
    {
        $query = ['
            SELECT
                q.[id],
                q.[object_id],
                o.[id] AS [map_object_id],
                o.[zipcode],
                o.[city],
                o.[city_part],
                o.[street],
                o.[street_desc_no],
                o.[street_orient_no],
                o.[street_orient_symbol]
            FROM %n q', $this->table, '
            JOIN [map_object] o ON (q.[object_id] = o.[object_id])
            ORDER BY %if', $priorityOnly, 'q.[priority], q.[id] %else q.[id] %end
            %lmt
        ',
            $batchLimit
        ];

        return $this->executeQuery($query)->fetchAll();
    }
}
