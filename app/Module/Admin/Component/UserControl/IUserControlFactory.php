<?php

namespace MP\Module\Admin\Component\UserControl;

/**
 * Generovana tovarna na UserControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IUserControlFactory
{
    /**
     * @return UserControl
     */
    public function create();
}
