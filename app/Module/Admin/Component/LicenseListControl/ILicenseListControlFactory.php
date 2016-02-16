<?php

namespace MP\Module\Admin\Component\LicenseListControl;

/**
 * Generovana tovarna na LicenseListControl.
 */
interface ILicenseListControlFactory
{
    /**
     * @return LicenseListControl
     */
    public function create();
}
