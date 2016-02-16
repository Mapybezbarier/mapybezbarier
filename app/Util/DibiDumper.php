<?php

namespace MP\Util;

use Dibi\Connection;

/**
 * Helper pro dumpovani SQL dotazu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DibiDumper
{
    /** @var Connection */
    public static $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        self::$connection = $connection;
    }

    /**
     * @param $query
     * @return bool
     */
    public static function dump($query)
    {
        return self::$connection->test($query);
    }
}
