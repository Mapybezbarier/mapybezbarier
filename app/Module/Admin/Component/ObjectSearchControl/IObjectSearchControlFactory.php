<?php

namespace MP\Module\Admin\Component\ObjectSearchControl;

/**
 * Generovana tovarna na ObjectSearchControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IObjectSearchControlFactory
{
    /**
     * @return ObjectSearchControl
     */
    public function create();
}
