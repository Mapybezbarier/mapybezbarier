<?php

namespace MP\Manager;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class GeocodingManager extends AbstractManager
{
    /**
     * Vrati zacatek fronty s prijoinovanymi informacemi o adrese objektu
     * @param bool $priorityOnly
     * @param int $batchLimit
     * @return \Dibi\Row[]
     */
    public function getQueue($priorityOnly, $batchLimit)
    {
        return $this->mapper->getQueue($priorityOnly, $batchLimit);
    }
}
