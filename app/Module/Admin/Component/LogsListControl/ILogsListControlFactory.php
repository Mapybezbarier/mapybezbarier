<?php

namespace MP\Module\Admin\Component\LogsList;

/**
 * Generovana tovarna na LogsListControl.
 */
interface ILogsListControlFactory
{
    /**
     * @return LogsListControl
     */
    public function create();
}
