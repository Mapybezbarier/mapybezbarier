<?php

namespace MP\Module\Admin\Component\ManualImportControl;

/**
 * Generovana tovarna na ManualImportControl.
 *
 */
interface IManualImportControlFactory
{
    /**
     * @return ManualImportControl
     */
    public function create();
}
