<?php

namespace MP\Module\Admin\Component\ImportLogsList;

/**
 * Generovana tovarna na ImportLogsListControl.
 */
interface IImportLogsListControlFactory
{
    /**
     * @return ImportLogsListControl
     */
    public function create();
}
