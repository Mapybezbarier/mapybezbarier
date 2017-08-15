<?php

namespace MP\Module\Admin\Component\ObjectSelectControl;

/**
 * Generovana tovarna na ObjectSelectControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IObjectSelectControlFactory
{
    /**
     * @return ObjectSelectControl
     */
    public function create();
}
