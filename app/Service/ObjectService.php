<?php

namespace MP\Service;

use MP\Manager\ElevatorManager;
use MP\Manager\IManager;
use MP\Manager\ObjectManager;
use MP\Manager\PlatformManager;
use MP\Manager\RampSkidsManager;
use MP\Manager\WcManager;
use MP\Mapper\IMapper;
use MP\Object\ObjectHelper;
use MP\Object\ObjectMetadata;
use MP\Util\Strings;
use MP\Util\Transaction\ITransaction;
use Nette\Utils\Paginator;

/**
 * Sluzba pro praci s mapovymi objekty.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectService
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

    /** @var ITransaction */
    protected $transaction;

    /** @var GeocodingService */
    protected $geocodingService;

    /**
     * @param ObjectManager $objectManager
     * @param RampSkidsManager $rampManager
     * @param ElevatorManager $elevatorManager
     * @param PlatformManager $platformManager
     * @param WcManager $wcManager
     * @param ITransaction $transaction
     * @param GeocodingService $geocodingService
     */
    public function __construct(
        ObjectManager $objectManager,
        RampSkidsManager $rampManager,
        ElevatorManager $elevatorManager,
        PlatformManager $platformManager,
        WcManager $wcManager,
        ITransaction $transaction,
        GeocodingService $geocodingService
    )
    {
        $this->objectManager = $objectManager;
        $this->rampManager = $rampManager;
        $this->elevatorManager = $elevatorManager;
        $this->platformManager = $platformManager;
        $this->wcManager = $wcManager;
        $this->transaction = $transaction;
        $this->geocodingService = $geocodingService;
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
        $object = $this->objectManager->findOneById($id) ?: [];

        $this->prepareObject($object);

        return $object;
    }

    /**
     * Vrati mapovy objekt
     *
     * @param int $id
     *
     * @return array
     */
    public function getObjectByObjectId($id)
    {
        $restrictor = [["[object_id] = %i", $id]];

        $object = $this->objectManager->findOneBy($restrictor) ?: [];

        $this->prepareObject($object);

        return $object;
    }

    /**
     * Vrati mapove objekty
     *
     * @param array|null $restrictor
     * @param array|null $order
     * @param Paginator|null $paginator
     *
     * @return array
     */
    public function getObjects($restrictor = null, $order = null, Paginator $paginator = null)
    {
        $objects = $this->objectManager->findAll($restrictor, $order, $paginator);

        foreach ($objects as &$object) {
            $this->prepareObject($object);
        }

        return $objects;
    }

    /**
     * Ulozi mapovy objekt.
     *
     * @param array $object
     * @param bool $priority
     *
     * @return array
     */
    public function saveObject(array $object, $priority = false)
    {
        $attachements = ObjectHelper::getAttachements($object);

        $object = $this->prepareObjectDataKeys($object);
        $object = $this->objectManager->persist($object);

        $this->saveObjectData($object, $attachements[ObjectMetadata::RAMP_SKIDS], $this->rampManager);
        $this->saveObjectData($object, $attachements[ObjectMetadata::PLATFORM], $this->platformManager);
        $this->saveObjectData($object, $attachements[ObjectMetadata::ELEVATOR], $this->elevatorManager);
        $this->saveObjectData($object, $attachements[ObjectMetadata::WC], $this->wcManager);

        $object = $this->objectManager->findOneById($object[IMapper::ID]);

        $this->geocodingService->checkGps($object, $priority);

        return $object;
    }

    /**
     * Ulozi pridruzena data mapoveho objektu.
     *
     * @param array $object
     * @param array $data
     * @param IManager $manager
     */
    protected function saveObjectData(array $object, array $data, IManager $manager)
    {
        foreach ($data as $item) {
            $item = $this->prepareObjectDataKeys($item);
            $item['map_object_id'] = $object['id'];

            $manager->persist($item);
        }
    }

    /**
     * Pripravi data objektu pro zapersistovani. Prevadi klice z camel case notace do potrzitkove.
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareObjectDataKeys(array $data)
    {
        $data = array_combine(
            array_map(Strings::class . '::toUnderscore', array_keys($data)),
            array_values($data)
        );

        return $data;
    }

    /**
     * Pripravi objekt pro vypis
     *
     * @param array $object
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
}
