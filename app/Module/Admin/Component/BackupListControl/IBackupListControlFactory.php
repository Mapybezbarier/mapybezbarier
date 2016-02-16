<?php

namespace MP\Module\Admin\Component\BackupList;

/**
 * Generovana tovarna na BackupListControl.
 */
interface IBackupListControlFactory
{
    /**
     * @return BackupListControl
     */
    public function create();
}
