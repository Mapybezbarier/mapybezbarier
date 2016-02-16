<?php

namespace MP\Module\Admin\Component\PasswordChangeControl;

/**
 * Generovana tovarna na PasswordChangeControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IPasswordChangeControlFactory
{
    /**
     * @return PasswordChangeControl
     */
    public function create();
}
