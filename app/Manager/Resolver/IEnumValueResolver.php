<?php

namespace MP\Manager\Resolver;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IEnumValueResolver
{
    /** @const Suffix sloupce s cizim klicem */
    const KEY_SUFFIX = '_id';

    /**
     * Dohledava hodnotu ciselniku pro data nactena z DB
     *
     * @param array $values
     * @return array
     */
    public function findResolve(array $values);

    /**
     * Dohledava hodnotu FK ciselniku pro data ukladana do DB
     *
     * @param array $values
     * @return array
     */
    public function persistResolve(array $values);

    /**
     * V udane tabulce dohleda normalni formu hodnoty.
     *
     * @param array $values
     * @return array
     */
    public function normalize(array $values);
}
