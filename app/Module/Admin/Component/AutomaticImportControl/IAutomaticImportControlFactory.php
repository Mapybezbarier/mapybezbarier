<?php

namespace MP\Module\Admin\Component\AutomaticImportControl;

/**
 * Generovana tovarna na AutomaticImportControl.
 *
 */
interface IAutomaticImportControlFactory
{
    /**
     * @return AutomaticImportControl
     */
    public function create();
}
