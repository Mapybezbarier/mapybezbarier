<?php

namespace MP\Module\Web\Component\DetailControl;

/**
 * Generovana tovarna pro DetailControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IDetailControlFactory
{
    /**
     * @return DetailControl
     */
    public function create();
}
