<?php

namespace MP\Module\Admin\Presenters;

use MP\Module\Admin\Component\BackupList\BackupListControl;
use MP\Module\Admin\Component\BackupList\IBackupListControlFactory;
use MP\Module\Admin\Component\LogsList\ILogsListControlFactory;
use MP\Module\Admin\Component\LogsList\LogsListControl;

/**
 * Systemovy modul
 *  vypis aktivit uzivatelu (default)
 *  vypis a stahovani zaloh DB
 */
class SystemPresenter extends AbstractAuthorizedPresenter
{
    /**
     * @param int|null $id
     */
    public function renderDefault($id = null)
    {
        $this->template->id = $id;
    }

    public function actionBackup()
    {}

    /**
     * @param ILogsListControlFactory $factory
     *
     * @return LogsListControl
     */
    protected function createComponentLogsList(ILogsListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IBackupListControlFactory $factory
     *
     * @return BackupListControl
     */
    protected function createComponentBackupList(IBackupListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
