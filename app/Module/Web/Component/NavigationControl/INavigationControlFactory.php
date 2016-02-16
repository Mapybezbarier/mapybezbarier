<?php

namespace MP\Module\Web\Component\NavigationControl;

/**
 * Generovana tovarna pro NavigationControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface INavigationControlFactory
{
    /**
     * @return NavigationControl
     */
    public function create();
}
