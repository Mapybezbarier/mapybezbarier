<?php

namespace MP\Module\Web\Presenters;

use MP\Module\Admin\Service\AutomaticImportService;
use MP\Service\GeocodingService;
use MP\Service\RuianSyncService;

/**
 * Presenter pro zpracovani CRON jobu
 * Kontroluje povolene IP adresy
 *
 */
class CronPresenter extends AbstractWebPresenter
{
    /** @var array IP adresy, ze kterych je mozne poustet CRON joby */
    private $allowedIps = [];

    /** @var GeocodingService @inject */
    public $geocodingService;

    /** @var RuianSyncService @inject */
    public $ruianSyncService;

    /** @var AutomaticImportService @inject */
    public $automaticImportService;

    /**
     * @override Overeni pristupu z povolene IP adresy.
     *
     * @param \Reflector $element
     *
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function checkRequirements($element)
    {
        parent::checkRequirements($element);

        if (!$this->runtimeMode->isDebugMode()) {
            $allowedIps = array_merge($this->allowedIps, [REMOTE_IP, SERVER_IP]);
            $remoteIp = $this->getHttpRequest()->getRemoteAddress();

            if (!in_array($remoteIp, $allowedIps, true)) {
                throw new \Nette\Application\ForbiddenRequestException;
            }
        }
    }

    /**
     * Davkove dohledani GPS souradnic z adres objektu
     * Poustet 1 za hodinu
     */
    public function actionGeocoding()
    {
        list($count, $report) = $this->geocodingService->processQueue();
        dump($count, $report);
        $this->terminate();
    }

    /**
     * Davkove dohledani GPS souradnic z adres objektu
     * Poustet 2. v mesici
     */
    public function actionRuianSync()
    {
        $count = $this->ruianSyncService->import();
        dump($count);
        $this->terminate();
    }

    /**
     * Automaticky import dle nastaveni v backendu
     * Poustet 1 za hodinu
     */
    public function actionImport()
    {
        $count = $this->automaticImportService->import();
        dump($count);
        $this->terminate();
    }
}
