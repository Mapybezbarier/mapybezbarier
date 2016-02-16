<?php

namespace MP\Module\Admin\Service;

use MP\Manager\ElevatorManager;
use MP\Manager\ExchangeSourceManager;
use MP\Manager\ImageManager;
use MP\Manager\IManager;
use MP\Manager\ObjectManager;
use MP\Manager\PlatformManager;
use MP\Manager\RampSkidsManager;
use MP\Manager\WcManager;
use MP\Mapper\Context;
use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\LicenseManager;
use MP\Object\ObjectMetadata;
use MP\Service\GeocodingService;
use MP\Util\Arrays;
use MP\Util\Transaction\ITransaction;
use Nette\Security\User;
use Nette\Utils\Json;
use Nette\Utils\Paginator;
use Tracy\Debugger;

/**
 * Sluzba pro praci s mapovymi objekty v kontextu administrace.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectService extends \MP\Service\ObjectService
{
    /** @var ObjectDraftService */
    protected $draftService;

    /** @var ImageManager */
    protected $imageManager;

    /** @var LogService */
    protected $logService;

    /** @var User */
    protected $user;

    /** @var UserService */
    protected $userService;

    /** @var Context */
    protected $context;

    /**
     * ObjectService constructor.
     *
     * @param ObjectManager $objectManager
     * @param RampSkidsManager $rampManager
     * @param ElevatorManager $elevatorManager
     * @param PlatformManager $platformManager
     * @param WcManager $wcManager
     * @param ITransaction $transaction
     * @param GeocodingService $geocodingService
     * @param ObjectDraftService $draftService
     * @param ImageManager $imageManager
     * @param LogService $logService
     * @param UserService $userService
     * @param User $user
     * @param Context $context
     */
    public function __construct(
        ObjectManager $objectManager,
        RampSkidsManager $rampManager,
        ElevatorManager $elevatorManager,
        PlatformManager $platformManager,
        WcManager $wcManager,
        ITransaction $transaction,
        GeocodingService $geocodingService,
        ObjectDraftService $draftService,
        ImageManager $imageManager,
        LogService $logService,
        UserService $userService,
        User $user,
        Context $context
    )
    {
        parent::__construct($objectManager, $rampManager, $elevatorManager, $platformManager, $wcManager, $transaction, $geocodingService);

        $this->draftService = $draftService;
        $this->imageManager = $imageManager;
        $this->logService = $logService;
        $this->userService = $userService;
        $this->user = $user;
        $this->context = $context;
    }

    /**
     * @override Pro vypis neni potrebda delat prepare.
     *
     * @param null $restrictor
     * @param null $order
     * @param Paginator|null $paginator
     *
     * @return array
     */
    public function getObjects($restrictor = null, $order = null, Paginator $paginator = null)
    {
        $objects = $this->objectManager->findAll($restrictor, $order, $paginator);

        return $objects;
    }

    /**
     * Vrati pocet objektu
     *
     * @param array|null $restrictor
     *
     * @return int
     */
    public function getObjectsCount($restrictor)
    {
        return $this->objectManager->findCount($restrictor);
    }

    /**
     * Vrati napovedi pro vyber objektu
     *
     * @param array|null $restrictor
     *
     * @return array
     */
    public function getObjectsSuggestions($restrictor)
    {
        return $this->objectManager->findSuggestions($restrictor);
    }

    /**
     * Vrati hodnoty objektu
     *
     * @param int $id
     *
     * @return array
     */
    public function getObjectValues($id)
    {
        $this->context->setMergeLanguageData(false);

        $object = $this->getObject($id);

        $this->context->setMergeLanguageData(true);

        return $object;
    }

    /**
     * Vrati hodnoty objektu
     *
     * @param int $id
     *
     * @return array
     */
    public function getObjectValuesByObjectId($id)
    {
        $this->context->setMergeLanguageData(false);

        $object = $this->getObjectByObjectId($id);

        $this->context->setMergeLanguageData(true);

        return $object;
    }


    /**
     * Pripravi hodnoty objektu pro vypis
     *
     * @param array $object
     *
     * @return mixed
     */
    protected function prepareObjectValues(&$object)
    {
        if ($object) {
            $this->findRelatedDataValues($object);
        }
    }

    /**
     * Dohleda k objektu data z navazanych priloh
     *
     * @param array &$object
     */
    protected function findRelatedDataValues(&$object)
    {
        $restrictor = [["[map_object_id] = %i", $object[IMapper::ID]]];

        $object['rampskids'] = $this->rampManager->getValues($restrictor);
        $object['platform'] = $this->platformManager->getValues($restrictor);
        $object['elevator'] = $this->elevatorManager->getValues($restrictor);
        $object['wc'] = $this->wcManager->getValues($restrictor);
    }

    /**
     * @override Ulozeni fotky a zalogovani akce.
     *
     * @param array $object
     * @param bool $priority
     * @param array $draft
     *
     * @return array
     */
    public function saveObject(array $object, $priority = false, array $draft = [])
    {
        $image = Arrays::get($object, ObjectMetadata::IMAGE, null);
        unset($object[ObjectMetadata::IMAGE]);

        $this->transaction->begin();

        $this->prepareSaveObject($object);

        if (!isset($object[IMapper::ID])) {
            $action = LogService::ACTION_OBJECT_CREATE;
        } else {
            $action = LogService::ACTION_OBJECT_EDIT;

            $object = $this->objectManager->backupObject($object);
        }

        $object = parent::saveObject($object, $priority);
        $object = $this->getObject($object[IMapper::ID]);

        $this->imageManager->persist($image, $object['object_id'], ImageManager::NAMESPACE_OBJECT);

        $this->draftService->publishDraft($draft, $object);

        $this->log($object, $action);

        $this->transaction->commit();

        $this->geocodingService->processQueue(true);

        return $object;
    }

    /**
     * Pripravi objekt pro ulozeni.
     *
     * @param array $object
     */
    protected function prepareSaveObject(array &$object)
    {
        $user = $this->userService->getUser($this->user->getId());

        $object['user_id'] = $user[IMapper::ID];
        $object['license_id'] = $user['license_id'] ?: LicenseManager::DEFAULT_ID;
        $object['source_id'] = ExchangeSourceManager::DEFAULT_ID;

        $object = $this->prepareObjectDataKeys($object);
    }

    /**
     * @override Nacteni fotky
     *
     * @param array $object
     */
    protected function prepareObject(&$object)
    {
        parent::prepareObject($object);

        if ($object) {
            $object[ObjectMetadata::IMAGE] = $this->imageManager->find($object['object_id'], ImageManager::NAMESPACE_OBJECT);
        }
    }

    /**
     * @override Pred ulozenim priloh stare smazat.
     *
     * @param array $object
     * @param array $data
     * @param IManager $manager
     */
    protected function saveObjectData(array $object, array $data, IManager $manager)
    {
        $restrictor = [
            ["[map_object_id] = %i", $object[IMapper::ID]]
        ];

        $manager->removeBy($restrictor);

        parent::saveObjectData($object, $data, $manager);
    }

    /**
     * @param array $object
     *
     * @throws \Nette\Utils\JsonException
     */
    public function removeObject(array $object)
    {
        $this->objectManager->remove($object['object_id']);
        $this->imageManager->remove($object['id'], ImageManager::NAMESPACE_OBJECT);

        $this->log($object, LogService::ACTION_OBJECT_DELETE);
    }

    /**
     * Spojeni objektu
     *
     * @param array $source
     * @param array $destination
     */
    public function joinObjects(array $source, array $destination)
    {
        $objectId = $this->objectManager->joinObjects($source['object_id'], $destination['object_id']);

        $removedObjectId = ($source['object_id'] == $objectId ? $destination['object_id'] : $source['object_id']);

        $this->imageManager->remove($removedObjectId, ImageManager::NAMESPACE_OBJECT);

        $this->log($source, LogService::ACTION_OBJECT_JOIN);
    }

    /**
     * Vraci seznam regionu pouzitych u vsech dosavadnich objektu
     *
     * @return array
     */
    public function getRegions()
    {
        $regionStats = $this->objectManager->getRegionsStats();

        return array_values(Arrays::pairs($regionStats, 'region', 'region'));
    }

    /**
     * Zaloguje akci nad objektem.
     *
     * @param array $object
     * @param string $action
     * @param string|null $data
     */
    protected function log(array $object, $action, $data = null)
    {
        if (null === $data) {
            try {
                $data = Json::encode(['id' => $object[IMapper::ID]]);
            } catch (\Nette\Utils\JsonException $e) {
                $data = null;

                Debugger::log($e);
            }
        }

        $this->logService->log(Authorizator::RESOURCE_OBJECT, $action, $object['object_id'], $object['title'], $data);
    }
}
