<?php

namespace MP\Module\Admin\Service;

use MP\Component\Mailer\IMessageFactory;
use MP\Exchange\Downloader\DownloaderFactory;
use MP\Exchange\Service\ImportLogger;
use MP\Exchange\Service\ImportService;
use MP\Manager\ExchangeSourceManager;
use MP\Manager\UserManager;
use MP\Mapper\IMapper;
use MP\Module\Admin\Component\ImportReportMailer\ImportReportMailer;
use MP\Module\Admin\Manager\AutomaticImportManager;
use MP\Module\Admin\Manager\ImportLogManager;
use MP\Manager\LicenseManager;
use Nette\Application\LinkGenerator;
use Nette\Utils\DateTime;
use Nette\Utils\Json;

/**
 * Sluzba pro automaticky import dat.
 */
class AutomaticImportService
{
    /** @var AutomaticImportManager */
    protected $manager;

    /** @var ImportService */
    protected $importService;

    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var LicenseManager */
    protected $licenseManager;

    /** @var ImportLogManager */
    protected $importLogManager;

    /** @var ImportReportMailer */
    protected $mailer;

    /** @var UserManager */
    protected $userManager;

    /** @var LinkGenerator */
    protected $linkGenerator;
    /**
     * @var LogService
     */
    protected $logService;

    /** @var DownloaderFactory */
    protected $downloaderFactory;

    /**
     * @param AutomaticImportManager $manager
     * @param ImportService $importService
     * @param ExchangeSourceManager $sourceManager
     * @param LicenseManager $licenseManager
     * @param ImportLogManager $importLogManager
     * @param UserManager $userManager
     * @param ImportReportMailer $mailer
     * @param LinkGenerator $linkGenerator
     * @param LogService $logService
     * @param DownloaderFactory $downloaderFactory
     */
    public function __construct(
        AutomaticImportManager $manager,
        ImportService $importService,
        ExchangeSourceManager $sourceManager,
        LicenseManager $licenseManager,
        ImportLogManager $importLogManager,
        UserManager $userManager,
        ImportReportMailer $mailer,
        LinkGenerator $linkGenerator,
        LogService $logService,
        DownloaderFactory $downloaderFactory
    ) {
        $this->manager = $manager;
        $this->importService = $importService;
        $this->sourceManager = $sourceManager;
        $this->licenseManager = $licenseManager;
        $this->importLogManager = $importLogManager;
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->linkGenerator = $linkGenerator;
        $this->logService = $logService;
        $this->downloaderFactory = $downloaderFactory;
    }

    /**
     * Projde nastavene automaticke importy a spusti ty, ktere se maji aktualne provest
     * Pokud import obsahuje nejake chyby nebo upozorneni, posle uzivateli, ktery import nastavil, odkaz na log
     * @return int pocet zpracovanych automatickych importu
     */
    public function import()
    {
        $items = $this->manager->findAll([
            "COALESCE([last_run], [created]) + interval '1 hour' * [hours_offset] < now()"
        ]);

        foreach ($items as $item) {
            $saveItem = [
                'last_run' => new DateTime(),
                'id' => $item['id'],
            ];

            if ($this->processItem($item)) {
                $saveItem['last_success'] = new DateTime();
            }

            $this->manager->persist($saveItem);
        }

        return count($items);
    }

    /**
     * @param array $item info o zdroji automatickeho importu
     * @return bool byly polozky naimportovany?
     */
    protected function processItem($item)
    {
        $ret = false;
        $logData = [];
        $logTitle = null;

        $source = $this->sourceManager->findOneBy([['[id] = %i', $item['source_id']]]);
        $downloader = $this->downloaderFactory->create($source);

        if ($downloader) {
            try {
                $data = $downloader->getData($item);
            } catch (\MP\Exchange\Exception\DownloadException $e) {
                ImportLogger::addError([], $e->getMessage());
                $data = null;
            }
        } else {
            $data = @file_get_contents($item['url']);
        }

        if ($data) {
            $license = $this->licenseManager->findOneBy([['[id] = %i', $item['license_id']]]);
            $logTitle = $source['title'];
            ImportLogger::reset();
            $this->importService->import($data, $source, $license, $item['certified'], $item['user_id']);

            if (!ImportLogger::getErrors()) {
                $ret = true;
            }

            if (ImportLogger::getErrors() || ImportLogger::getCount()) {
                $values = [
                    'data' => ImportLogger::getPersistData(),
                    'import_id' => $item['id'],
                    'count' => ImportLogger::getCount(),
                ];
                $log = $this->importLogManager->persist($values);
                $this->sendImportReport($item['user_id'], $log['id'], $source);
                $logData['logId'] = $log['id'];
            }
        }

        $this->logService->log(
            Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_AUTO_RUN,
            $item['id'], $logTitle, Json::encode($logData), $item['user_id']
        );

        return $ret;
    }

    /**
     * Uzivateli, ktery nastavil import, posle odkaz na report
     * @param int $userId
     * @param int $logId
     * @param \Dibi\Row $source
     */
    protected function sendImportReport($userId, $logId, $source)
    {
        $user = $this->userManager->findOneBy([['id = %i', $userId]]);

        $this->mailer->send([
            IMessageFactory::TO => $user['email'],
            IMessageFactory::DATA => [
                'link' => $this->linkGenerator->link('Admin:Import:logs', ['id' => $logId]),
                'sourceTitle' => $source['title'],
                'sourceType' => $source['format'],
            ],
            IMessageFactory::SUBJECT => 'backend.control.import.mailer.subject',
        ]);
    }

    /**
     * @param array $values
     */
    public function create($values)
    {
        $values = $this->manager->persist($values);
        $this->logService->log(Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_AUTO_CREATE, $values[IMapper::ID]);
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        $this->manager->remove($id);
        $this->logService->log(Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_AUTO_DELETE, $id);
    }
}
