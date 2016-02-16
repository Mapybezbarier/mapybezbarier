<?php

namespace MP\Module\Admin\Component\ObjectListControl;

/**
 * Generovana tovarna na ObjectListControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IObjectListControlFactory
{
    /**
     * @return ObjectListControl
     */
    public function create();
}
