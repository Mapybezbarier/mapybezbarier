<?php

namespace MP\Module\Admin\Component\PasswordResetControl;

/**
 * Generovana tovarna na PasswordResetControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IPasswordResetControlFactory
{
    /**
     * @return PasswordResetControl
     */
    public function create();
}
