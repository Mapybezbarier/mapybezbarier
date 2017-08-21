<?php

namespace MP\Module\Admin\Service;
use MP\Manager\ExchangeSourceManager;
use MP\Manager\UserManager;
use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\AutomaticImportManager;
use MP\Module\Admin\Manager\ImportLogManager;
use Nette\Utils\Json;

/**
 * Sluzba pro praci s logem auotmatickeho i manualniho importu.
 */
class ImportLogService
{
    /** @var ImportLogManager */
    protected $manager;

    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var AutomaticImportManager */
    protected $automaticImportManager;

    /** @var UserManager */
    protected $userManager;

    /** @see ImportLogger::getPersistData */
    protected static $log = [
        'errors' => [],
        'notices' => [],
        'objects' => [],
    ];

    /**
     * @param ImportLogManager $manager
     * @param ExchangeSourceManager $sourceManager
     * @param AutomaticImportManager $automaticImportManager
     * @param UserManager $userManager
     */
    public function __construct(
        ImportLogManager $manager, ExchangeSourceManager $sourceManager,
        AutomaticImportManager $automaticImportManager, UserManager $userManager
    ) {
        $this->manager = $manager;
        $this->sourceManager = $sourceManager;
        $this->automaticImportManager = $automaticImportManager;
        $this->userManager = $userManager;
    }

    /**
     * Vraci pripravena data pro vypis logu
     * @return array
     */
    public function findListData()
    {
        $ret = $this->manager->findAll(null, ['created' => IMapper::ORDER_DESC]);

        foreach ($ret as &$item) {
            if ($item['import_id']) {
                $importSettings = $this->automaticImportManager->findOneBy([['id = %i', $item['import_id']]]);
                $item['source'] = $importSettings['source'];
                $item['user'] = $importSettings['user'];
            } else {
                $importSettings = Json::decode($item['manual_settings']);
                $source = $this->sourceManager->findOneBy([['id = %i', $importSettings->source_id]]);
                $user = $this->userManager->findOneBy([['id = %i', $importSettings->user_id]]);
                $item['source'] = $source['title'];
                $item['user'] = $user['login'];
            }

            $this->parseLog($item['data']);
            $item['errors_count'] = count(self::$log['errors']);
            $item['notices_count'] = count(self::$log['notices']);
        }

        unset($item);

        return $ret;
    }

    /**
     * Pripravi log importu k vypisu - seskupi chyby a upozorneni dle objektu
     * @param int $id
     * @return array 3prvkove pole (array objekty, bool ma chyby?, int importovany pocet)
     */
    public function prepareLog($id)
    {
        $log = [];
        $item = $this->manager->findOneBy([['id = %i', $id]]);

        if ($item) {
            $this->parseLog($item['data']);

            foreach (self::$log['objects'] as $key => $object) {
                $log[$key]['data'] = $object;

                $log[$key]['errors'] = [];
                $log[$key]['notices'] = [];

                foreach (self::$log['errors'] as $error) {
                    if ($error['object'] === $key) {
                        $log[$key]['errors'][] = $error;
                    }
                }

                foreach (self::$log['notices'] as $notice) {
                    if ($notice['object'] === $key) {
                        $log[$key]['notices'][] = $notice;
                    }
                }
            }
        }

        return [
            'log' => $log,
            'hasErrors' => (count(self::$log['errors']) > 0),
            'count' => $item['count'],
        ];
    }

    /**
     * Rozparsuje ulozeny log
     * @param json $data
     */
    protected function parseLog($data)
    {
        self::$log = Json::decode($data, Json::FORCE_ARRAY);
    }
}
