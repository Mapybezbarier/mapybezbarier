<?php
/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */

namespace MP\Exchange\Parser;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IParser
{
    /** @const Typ parseru */
    const TYPE_INTERNAL = 'internal',
        TYPE_EXTERNAL = 'external';

    /**
     * Naparasuje vstupni data do unifikovane struktury.
     *
     * @param mixed $data
     * @return array
     */
    public function parse($data);

    /**
     * Vrati typ parseru.
     *
     * @return string
     */
    public function getType();
}
