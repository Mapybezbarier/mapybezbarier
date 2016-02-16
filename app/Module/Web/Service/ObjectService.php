<?php

namespace MP\Module\Web\Service;

use MP\Mapper\ObjectMapper;

/**
 * Sluzba pro praci s mapovymi objekty v kontextu frontendu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectService extends \MP\Service\ObjectService
{
    /**
     * @override Priprava klicu objektu a zaverzovani existujiciho objektu.
     *
     * @param array $object
     * @param bool $priority
     * @param array $current
     *
     * @return array
     */
    public function saveObject(array $object, $priority = false, $current = null)
    {
        if ($current) {
            $this->objectManager->backupObject($current);

            $object[ObjectMapper::OBJECT_ID] = $current[ObjectMapper::OBJECT_ID];
        }

        return parent::saveObject($object, $priority);
    }

    /**
     * Vrati aktualni mapovy objekt podle jineho mapoveho objektu.
     *
     * @param array $object
     *
     * @return array|null
     */
    public function getCurrentObject(array $object)
    {
        $object = $this->prepareObjectDataKeys($object);

        $object = $this->objectManager->findByObject($object);

        return $object;
    }

    /**
     * Odstrani vsechny mapove objekty pochazejici z udaneho zdroje.
     *
     * @param array $source
     */
    public function removeObjectsBySource($source)
    {
        $this->objectManager->removeObjectsBySource($source);
    }
}
