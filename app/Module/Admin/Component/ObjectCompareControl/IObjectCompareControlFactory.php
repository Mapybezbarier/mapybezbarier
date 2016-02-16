<?php

namespace MP\Module\Admin\Component\ObjectCompareControl;

/**
 * Generovana tovarna na ObjectCompareControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IObjectCompareControlFactory
{
    /**
     * @return ObjectCompareControl
     */
    public function create();
}
