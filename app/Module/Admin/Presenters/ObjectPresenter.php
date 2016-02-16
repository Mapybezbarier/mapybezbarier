<?php

namespace MP\Module\Admin\Presenters;

use MP\Component\FlashMessageControl;
use MP\Mapper\IMapper;
use MP\Module\Admin\Component\ObjectControl\IObjectControlFactory;
use MP\Module\Admin\Component\ObjectControl\ObjectControl;
use MP\Module\Admin\Component\ObjectHistoryControl\IObjectHistoryControlFactory;
use MP\Module\Admin\Component\ObjectHistoryControl\ObjectHistoryControl;
use MP\Module\Admin\Component\ObjectListControl\IObjectListControlFactory;
use MP\Module\Admin\Component\ObjectListControl\ObjectListControl;
use MP\Module\Admin\Component\ObjectSelectControl\IObjectSelectControlFactory;
use MP\Module\Admin\Component\ObjectSelectControl\ObjectSelectControl;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\ObjectDraftService;
use MP\Module\Admin\Service\ObjectService;

/**
 * Presenter pro spravu mapovych objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectPresenter extends AbstractObjectPresenter
{
    /** @const Nazev komponenty s formularem objektu. */
    const COMPONENT_OBJECT = 'object';

    /** @const Nazev komponenty s vyberem objektu. */
    const COMPONENT_OBJECT_SELECT = 'objectSelect';

    /** @const Nazev parametru nesouci ID objektu. */
    const PARAM_ID = 'id';

    /** @var ObjectService @inject */
    public $objectService;

    /** @var ObjectDraftService @inject */
    public $draftService;

    /** @var array */
    protected $object;

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->object = $this->object;
    }

    public function actionSelect()
    {
        $this[self::COMPONENT_OBJECT_SELECT]->onObjectSelected[] = function($values) {
            $this->resolveMappingAction($values);
        };
    }

    /**
     * @param array $values
     *
     * @throws \Nette\Application\BadRequestException
     */
    protected function resolveMappingAction(array $values)
    {
        if ($id = $values[ObjectSelectControl::COMPONENT_ID]) {
            $object = $this->objectService->getObjectByObjectId($id);

            try {
                $this->checkOwnership($object);
                $isOwner = true;
            } catch (\Nette\Application\ForbiddenRequestException $e) {
                $isOwner = false;
            }

            if ($isOwner) {
                $draft = $this->draftService->createByObject($object);

                $this->redirect(':Admin:Draft:default', ['id' => $draft[IMapper::ID], 'mapping' => true]);
            } else {
                // necertifikovana agentura/mapar nemuzou mapovat objekty jineho uzivatele -> vytvoreni noveho objektu se stejnym nazvem
                if (
                    false === $this->userService->isCertified($this->getUser()->getId())
                    && (
                        $this->getUser()->isInRole(Authorizator::ROLE_AGENCY)
                        || $this->getUser()->isInRole(Authorizator::ROLE_MAPPER)
                    )
                ) {
                    $draft = $this->draftService->createByTitle($values[ObjectSelectControl::COMPONENT_TITLE]);

                    $this->redirect(':Admin:Draft:default', ['id' => $draft[IMapper::ID]]);
                } else { // uzivatel chce prevzit objekt od jineho uzivatele
                    $object = [
                        IMapper::ID => $object['id'],
                        'object_id' => $object['object_id']
                    ];

                    $draft = $this->draftService->createByObject($object);

                    $this->redirect(':Admin:Draft:default', ['id' => $draft[IMapper::ID]]);
                }
            }
        } else if ($title = $values[ObjectSelectControl::COMPONENT_TITLE]) {
            $draft = $this->draftService->createByTitle($title);

            $this->redirect(':Admin:Draft:default', ['id' => $draft[IMapper::ID]]);
        } else {
            throw new \Nette\Application\BadRequestException("Object ID or title is missing");
        }
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
    {
        $this->object = $this->prepareObject($id);

        if ($draft = $this->draftService->getDraftByKey($this->object['object_id'])) {
            $this->redirect(':Admin:Draft:default', ['id' => $draft[IMapper::ID]]);
        }

        $this[self::COMPONENT_OBJECT]->setObject($this->object);
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionHistory($id)
    {
        $this->object = $this->prepareObject($id);
    }

    /**
     * @param int $id
     */
    public function actionJoin($id)
    {
        $this->object = $this->prepareObject($id);

        $this[self::COMPONENT_OBJECT_SELECT]->setId($id);
        $this[self::COMPONENT_OBJECT_SELECT]->onObjectSelected[] = function($values) {
            if ($selected = $values[ObjectSelectControl::COMPONENT_ID]) {
                $this->objectService->joinObjects($this->object, $this->prepareObject($selected));

                $this->flashMessage('backend.object.flash.join.success', FlashMessageControl::TYPE_SUCCESS);

                $this->redirect('default');
            } else {
                throw new \Nette\Application\BadRequestException("Object ID is missing");
            }
        };
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \Nette\Application\BadRequestException
     */
    protected function prepareObject($id)
    {
        $object = $this->objectService->getObjectByObjectId($id);

        if ($object) {
            $this->checkOwnership($object);
        } else {
            throw new \Nette\Application\BadRequestException("Object with ID '{$id}' not found");
        }

        return $object;
    }

    /**
     * @param IObjectListControlFactory $factory
     *
     * @return ObjectListControl
     */
    protected function createComponentObjectList(IObjectListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IObjectSelectControlFactory $factory
     *
     * @return ObjectSelectControl
     */
    protected function createComponentObjectSelect(IObjectSelectControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IObjectControlFactory $factory
     *
     * @return ObjectControl
     */
    protected function createComponentObject(IObjectControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IObjectHistoryControlFactory $factory
     *
     * @return ObjectHistoryControl
     */
    protected function createComponentObjectHistory(IObjectHistoryControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
