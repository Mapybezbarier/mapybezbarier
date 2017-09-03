<?php

namespace MP\Module\Admin\Presenters;

use MP\Module\Admin\Component\AutomaticImportControl\AutomaticImportControl;
use MP\Module\Admin\Component\AutomaticImportControl\IAutomaticImportControlFactory;
use MP\Module\Admin\Component\AutomaticImportList\AutomaticImportListControl;
use MP\Module\Admin\Component\AutomaticImportList\IAutomaticImportListControlFactory;
use MP\Module\Admin\Component\ImportLogsList\IImportLogsListControlFactory;
use MP\Module\Admin\Component\ImportLogsList\ImportLogsListControl;
use MP\Module\Admin\Component\LicenseControl\ILicenseControlFactory;
use MP\Module\Admin\Component\LicenseControl\LicenseControl;
use MP\Module\Admin\Component\LicenseListControl\ILicenseListControlFactory;
use MP\Module\Admin\Component\LicenseListControl\LicenseListControl;
use MP\Module\Admin\Component\ManualImportControl\IManualImportControlFactory;
use MP\Module\Admin\Component\ManualImportControl\ManualImportControl;

/**
 * Sprava automatickych importu, provadeni manualniho importu.
 */
class ImportPresenter extends AbstractAuthorizedPresenter
{
    /** @const Nazev komponenty licence. */
    const COMPONENT_LICNSE = 'license';

    /**
     * @param int|null $id
     */
    public function renderLogs($id = null)
    {
        $this->template->id = $id;
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionLicenseEdit($id)
    {
        $this[self::COMPONENT_LICNSE]->setId($id);
    }

    /**
     * @param IAutomaticImportListControlFactory $factory
     *
     * @return AutomaticImportListControl
     */
    protected function createComponentAutomaticImportList(IAutomaticImportListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IImportLogsListControlFactory $factory
     *
     * @return ImportLogsListControl
     */
    protected function createComponentImportLogsList(IImportLogsListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IAutomaticImportControlFactory $factory
     *
     * @return AutomaticImportControl
     */
    protected function createComponentAutomaticImport(IAutomaticImportControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IManualImportControlFactory $factory
     *
     * @return ManualImportControl
     */
    protected function createComponentManualImport(IManualImportControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param ILicenseListControlFactory $factory
     *
     * @return LicenseListControl
     */
    protected function createComponentLicenseList(ILicenseListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param ILicenseControlFactory $factory
     *
     * @return LicenseControl
     */
    protected function createComponentLicense(ILicenseControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
