<?php

namespace MP\Module\Admin\Component\ObjectControl;

/**
 * Generovana tovarna na ObjectControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IObjectControlFactory
{
    /**
     * @return ObjectControl
     */
    public function create();
}
