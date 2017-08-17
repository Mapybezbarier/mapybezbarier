<?php

namespace MP\Module\Admin\Service;

use MP\Exchange\Downloader\DownloaderFactory;
use MP\Exchange\Service\ImportLogger;
use MP\Exchange\Service\ImportService;
use MP\Manager\ExchangeSourceManager;
use MP\Module\Admin\Manager\ImportLogManager;
use MP\Module\Admin\Manager\LicenseManager;
use Nette\Http\FileUpload;
use Nette\Utils\Json;

/**
 * Sluzba pro maunalni import dat.
 */
class ManualImportService
{
    /** @var ImportService */
    protected $importService;

    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var LicenseManager */
    protected $licenseManager;

    /** @var ImportLogManager */
    protected $importLogManager;
    /**
     * @var LogService
     */
    protected $logService;

    /** @var DownloaderFactory */
    protected $downloaderFactory;

    /**
     * @param ImportService $importService
     * @param ExchangeSourceManager $sourceManager
     * @param LicenseManager $licenseManager
     * @param ImportLogManager $importLogManager
     * @param LogService $logService
     * @param DownloaderFactory $downloaderFactory
     */
    public function __construct(
        ImportService $importService,
        ExchangeSourceManager $sourceManager,
        LicenseManager $licenseManager,
        ImportLogManager $importLogManager,
        LogService $logService,
        DownloaderFactory $downloaderFactory
    ) {
        $this->importService = $importService;
        $this->sourceManager = $sourceManager;
        $this->licenseManager = $licenseManager;
        $this->importLogManager = $importLogManager;
        $this->logService = $logService;
        $this->downloaderFactory = $downloaderFactory;
    }

    /**
     * Provede import dle predanych dat z formulare manualniho importu
     * Primarne zpracuje pripadne nahrany soubor a az nasledne pripadnou URL
     * @param array $formValues
     * @param int $userId
     * @return array (boolean je v datech chyba kvality?, int ID pripadneho logu)
     */
    public function import($formValues, $userId)
    {
        $ret = [
            'hasErrors' => false,
            'logId' => null,
        ];

        $logData = [];

        $source = $this->sourceManager->findOneBy([['[id] = %i', $formValues['source_id']]]);

        /** @var FileUpload $file */
        $file = $formValues['file'];

        if ($file->getError() === UPLOAD_ERR_NO_FILE) {
            $downloader = $this->downloaderFactory->create($source);

            if ($downloader) {
                try {
                    $data = $downloader->getData($formValues);
                } catch (\MP\Exchange\Exception\DownloadException $e) {
                    ImportLogger::addError([], $e->getMessage());
                    $data = null;
                }
            } else {
                $data = @file_get_contents($formValues['url']);
            }
        } else {
            $data = $file->getContents();
        }

        if ($data) {
            $license = $this->licenseManager->findOneBy([['[id] = %i', $formValues['license_id']]]);
            $this->importService->import($data, $source, $license, $formValues['certified'], $userId, true);
        }

        if (ImportLogger::getErrors() || ImportLogger::getCount()) {
            $log = [
                'data' => ImportLogger::getPersistData(),
                'manual_settings' => Json::encode(array_merge($formValues, ['user_id' => $userId])),
                'count' => ImportLogger::getCount(),
            ];
            $log = $this->importLogManager->persist($log);
            $ret = [
                'hasErrors' => ImportLogger::hasErrors(),
                'logId' => $log['id'],
            ];
            $logData['logId'] = $log['id'];
        }

        $this->logService->log(
            Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_MANUAL,
            null, null, Json::encode($logData)
        );

        return $ret;
    }
}
