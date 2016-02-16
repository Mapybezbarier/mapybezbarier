<?php

namespace MP\Module\Admin\Component\LoginControl;

/**
 * Generovana tovarna na LoginControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface ILoginControlFactory
{
    /**
     * @return LoginControl
     */
    public function create();
}
