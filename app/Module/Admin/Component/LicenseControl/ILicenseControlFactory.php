<?php

namespace MP\Module\Admin\Component\LicenseControl;

/**
 * Generovana tovarna na LicenseControl.
 *
 */
interface ILicenseControlFactory
{
    /**
     * @return LicenseControl
     */
    public function create();
}
