<?php

namespace MP\Module\Admin\Component\AutomaticImportList;

/**
 * Generovana tovarna na AutomaticImportListControl.
 */
interface IAutomaticImportListControlFactory
{
    /**
     * @return AutomaticImportListControl
     */
    public function create();
}
