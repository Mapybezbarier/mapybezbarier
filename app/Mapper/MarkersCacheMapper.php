<?php

namespace MP\Mapper;

/**
 * Mapper pro tabulku s cache markeru pripravenych pro vypis
 */
class MarkersCacheMapper extends DatabaseMapper
{
    const EXPIRE_SECONDS = 24 * 60 * 60;

    /**
     * @param string $key
     * @param int $time
     * @return false|string
     */
    public function read($key, $time)
    {
        $slidedExpire = $time + self::EXPIRE_SECONDS;
        $query = ["
            UPDATE [service].[markers_cache]
            SET [expire] = %t
            WHERE [key] = %bin
            RETURNING data
        ", $slidedExpire, $key];

        return $this->executeQuery($query)->fetchSingle();
    }

    /**
     * @param string $key
     * @param string $data
     * @param int $time
     */
    public function write($key, $data, $time)
    {
        $expire = $time + self::EXPIRE_SECONDS;
        $query = ["
            INSERT INTO [service].[markers_cache] ([key], [data], [expire])
            VALUES (%bin, %s, %t)
            ON CONFLICT ([key])
            DO UPDATE SET [data] = %s, [expire] = %t
        ", $key, $data, $expire, $data, $expire];

        $this->executeQuery($query);
    }

    /**
     * @param string|null $key
     */
    public function remove($key = null)
    {
        if ($key) {
            $query = ["
                DELETE FROM [service].[markers_cache]
                WHERE [key] = %s
            ", $key];
        } else {
            $query = ["
                DELETE FROM [service].[markers_cache]
            "];
        }

        $this->executeQuery($query);
    }
}