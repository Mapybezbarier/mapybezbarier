<?php

namespace MP\Module\Admin\Component\RegistrationControl;

/**
 * Generovana tovarna pro RegistrationControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IRegistrationControlFactory
{
    /**
     * @return RegistrationControl
     */
    public function create();
}
