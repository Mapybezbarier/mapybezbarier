<?php

namespace MP\Manager;

/**
 * Rozhranni manazeru, ktere jsou schopny ukladat jazykove zavisla data
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface ILangAwareManager
{
    /**
     * @param int $id
     * @param string $lang
     * @param array $data
     */
    public function persistLangData($id, $lang, array $data);
}
