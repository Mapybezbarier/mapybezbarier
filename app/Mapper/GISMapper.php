<?php

namespace MP\Mapper;

/**
 * Mapper pro praci s GIS souradnicemi
 */
class GISMapper extends DatabaseMapper
{
    /**
     * Prevod souradnice S-JTSK na WGS-84 (GPS)
     * SRID 36 -> 4325
     * @param float $xSJTSK
     * @param float $ySJTSK
     * @return \Dibi\Row
     */
    public function transformSJTSKToGps($xSJTSK, $ySJTSK)
    {
        $query = ["
            SELECT ST_X(wgs_point) AS longitude, ST_Y(wgs_point) AS latitude
            FROM (
                SELECT ST_Transform(ST_GeomFromText('POINT({$xSJTSK} {$ySJTSK})', 5514), 4326) AS wgs_point
            ) w
        "];

        return $this->executeQuery($query)->fetch();
    }
}
