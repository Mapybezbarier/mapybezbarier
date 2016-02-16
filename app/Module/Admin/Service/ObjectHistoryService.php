<?php

namespace MP\Module\Admin\Service;

use MP\Manager\ImageManager;
use MP\Mapper\Context;
use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\Version\ElevatorManager;
use MP\Module\Admin\Manager\Version\ObjectManager;
use MP\Module\Admin\Manager\Version\PlatformManager;
use MP\Module\Admin\Manager\Version\RampSkidsManager;
use MP\Module\Admin\Manager\Version\WcManager;
use Nette\Utils\Json;
use Nette\Utils\Paginator;
use Tracy\Debugger;

/**
 * Sluzba pro verze mapovych objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectHistoryService
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var RampSkidsManager */
    protected $rampManager;

    /** @var ElevatorManager */
    protected $elevatorManager;

    /** @var PlatformManager */
    protected $platformManager;

    /** @var WcManager */
    protected $wcManager;

    /** @var ImageManager */
    protected $imageManager;

    /** @var LogService */
    protected $logService;

    /** @var Context */
    protected $context;

    /**
     * @param ObjectManager $objectManager
     * @param RampSkidsManager $rampManager
     * @param ElevatorManager $elevatorManager
     * @param PlatformManager $platformManager
     * @param WcManager $wcManager
     * @param ImageManager $imageManager
     * @param LogService $logService
     * @param Context $context
     */
    public function __construct(
        ObjectManager $objectManager,
        RampSkidsManager $rampManager,
        ElevatorManager $elevatorManager,
        PlatformManager $platformManager,
        WcManager $wcManager,
        ImageManager $imageManager,
        LogService $logService,
        Context $context
    )
    {
        $this->objectManager = $objectManager;
        $this->rampManager = $rampManager;
        $this->elevatorManager = $elevatorManager;
        $this->platformManager = $platformManager;
        $this->wcManager = $wcManager;
        $this->imageManager = $imageManager;
        $this->logService = $logService;
        $this->context = $context;
    }

    /**
     * Vrati mapovy objekt
     *
     * @param int $id
     *
     * @return array
     */
    public function getObject($id)
    {
        $this->context->setMergeLanguageData(false);

        $object = $this->objectManager->findOneById($id) ?: [];

        $this->prepareObject($object);

        $this->context->setMergeLanguageData(true);

        return $object;
    }

    /**
     * Vrati mapove objekty
     *
     * @param array $object
     * @param Paginator|null $paginator
     *
     * @return array
     */
    public function getObjects(array $object, Paginator $paginator = null)
    {
        $restrictor = [["[object_id] = %i", $object['object_id']]];

        $objects = $this->objectManager->findAll($restrictor, ['modified_date' => IMapper::ORDER_DESC], $paginator);

        return $objects;
    }

    /**
     * Obnova verze objektu.
     *
     * @param array $object
     */
    public function revertObject(array $object)
    {
        $this->objectManager->revertObject($object[IMapper::ID]);

        $this->log($object, LogService::ACTION_OBJECT_REVERT);
    }

    /**
     * Rozdeleni objektu
     *
     * @param array $object
     *
     * @return int
     */
    public function splitObject(array $object)
    {
        $objectId = $this->objectManager->splitObject($object[IMapper::ID]);

        $this->imageManager->copy($object['object_id'], $objectId);

        $this->log($object, LogService::ACTION_OBJECT_SPLIT);

        return $objectId;
    }

    /**
     * Pripravi objekt pro vypis
     *
     * @param array $object
     *
     * @return mixed
     */
    protected function prepareObject(&$object)
    {
        if ($object) {
            $this->findRelatedData($object);
        }
    }

    /**
     * Dohleda k objektu data z navazanych priloh
     *
     * @param array &$object
     */
    protected function findRelatedData(&$object)
    {
        $restrictor = [["[map_object_id] = %i", $object[IMapper::ID]]];

        $object['rampskids'] = $this->rampManager->findAll($restrictor);
        $object['platform'] = $this->platformManager->findAll($restrictor);
        $object['elevator'] = $this->elevatorManager->findAll($restrictor);
        $object['wc'] = $this->wcManager->findAll($restrictor);
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
