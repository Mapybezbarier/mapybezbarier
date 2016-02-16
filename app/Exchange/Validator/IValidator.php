<?php

namespace MP\Exchange\Validator;


/**
 * Rozhranni validatoru importovanych mapovych objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IValidator
{
    /** @const Typy validatoru. */
    const TYPE_QUALITY = 'quality',
        TYPE_CONSISTENCY = 'consistency';

    /**
     * Zvaliduje importovany mapovy objekt.
     *
     * @param array $object
     */
    public function validate(array $object);

    /**
     * Vrati format importovanych dat, ktere dokaze validovat. Pokud vraci null, pak dokaze validovat libovolny format.
     *
     * @return string|null
     */
    public function getFormat();

    /**
     * Vrati typ validatoru. Pouziva se pro serazeni validatoru. Prvni jsou validatory kvality, az pote konzistentce.
     *
     * @return string
     */
    public function getType();
}
