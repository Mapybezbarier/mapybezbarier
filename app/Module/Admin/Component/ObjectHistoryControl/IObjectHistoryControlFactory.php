<?php

namespace MP\Module\Admin\Component\ObjectHistoryControl;

/**
 * Generovana tovarna na ObjectHistoryControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IObjectHistoryControlFactory
{
    /**
     * @return ObjectHistoryControl
     */
    public function create();
}
