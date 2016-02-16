<?php

namespace MP\Module\Web\Component;

/**
 * Generovana tovarna pro ExportControl.
 *
 */
interface IExportControlFactory
{
    /**
     * @return ExportControl
     */
    public function create();
}
