<?php

namespace MP\Exchange\Service;
use Nette\Utils\Json;

/**
 * Logger pro import
 * Rozlisuje error a notice
 */
class ImportLogger
{
    protected static $errors = [];
    protected static $notices = [];
    protected static $count = 0;

    /**
     * Vynuluje informace z predchozich importu
     */
    public static function reset()
    {
        self::$errors = [];
        self::$notices = [];
        self::$count = 0;
    }

    /**
     * Zaloguje error
     *
     * @param array $object
     * @param string $message
     * @param array $arguments
     */
    public static function addError($object, $message, $arguments = [])
    {
        self::$errors[] = [
            'object' => self::getObjectSimpleInfo($object),
            'message' => $message,
            'arguments' => $arguments,
        ];
    }

    /**
     * Zaloguje notice
     *
     * @param array $object
     * @param string $message
     * @param array $arguments
     */
    public static function addNotice($object, $message, $arguments = [])
    {
        self::$notices[] = [
            'object' => self::getObjectSimpleInfo($object),
            'message' => $message,
            'arguments' => $arguments,
        ];
    }

    /**
     * Zaloguje notice z kontroly konzistence - ve vypise bude oddeleno
     *
     * @param array $object
     * @param string $message
     * @param array $arguments
     */
    public static function addConsistencyNotice($object, $message, $arguments = [])
    {
        $message = 'consistency.'.$message;
        self::addNotice($object, $message, $arguments);
    }


    /**
     * Nastavi celkovy pocet importovanych objektu
     * @param int $count
     */
    public static function setCount($count)
    {
        self::$count = $count;
    }

    /**
     * @return array
     */
    public static function getErrors()
    {
        return self::$errors;
    }

    /**
     * @return array
     */
    public static function getNotices()
    {
        return self::$notices;
    }

    /**
     * @return array
     */
    public static function getCount()
    {
        return self::$count;
    }

    /**
     * @return bool
     */
    public static function hasErrors()
    {
        return (bool) self::$errors;
    }

    /**
     * @return bool
     */
    public static function hasNotices()
    {
        return (bool) self::$notices;
    }

    /**
     * Zalogovani informaci o objektu
     *
     * @param array $object
     *
     * @return array
     */
    protected static function getObjectSimpleInfo($object)
    {
        return $object;
    }

    /**
     * Pripravi data pro ulozeni do DB
     * Cilem je odstarnit pripadne duplicity objektu
     * @return string JSON
     */
    public static function getPersistData()
    {
        $ret = [
            'errors' => [],
            'notices' => [],
            'objects' => [],
        ];

        foreach (self::getErrors() as $item) {
            $ret['errors'][] = self::preparePersistDataItem($item, $ret);
        }

        foreach (self::getNotices() as $item) {
            $ret['notices'][] = self::preparePersistDataItem($item, $ret);
        }

        return Json::encode($ret);
    }

    /**
     * Vytahuje objekty z jednotlivych polozek logu
     * @param $item
     * @param $ret
     *
     * @return array
     */
    protected static function preparePersistDataItem($item, &$ret)
    {
        // json_encode pouze pro jedinecny otisk, rychlejsi nez serialize
        $id = md5(json_encode($item['object']));

        if (!isset($ret['objects'][$id])) {
            $ret['objects'][$id] = $item['object'];
        }

        $item['object'] = $id;

        return $item;
    }
}
