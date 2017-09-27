<?php

namespace MP\Module\Admin\Component\ObjectListControl;

use MP\Component\AbstractControl;
use MP\Mapper\IMapper;
use MP\Module\Admin\Component\ObjectSearchControl\IObjectSearchControlFactory;
use MP\Module\Admin\Component\ObjectSearchControl\ObjectSearchControl;
use MP\Module\Admin\Component\PaginatorControl\IPaginatorControlFactory;
use MP\Module\Admin\Component\PaginatorControl\PaginatorControl;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\ObjectDraftService;
use MP\Module\Admin\Service\ObjectRestrictorBuilder;
use MP\Module\Admin\Service\ObjectService;
use Nette\Utils\Paginator;

/**
 * Komponenta pro vykresleni mapovych objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectListControl extends AbstractControl
{
    /** @const Nazev komponenty paginatoru */
    const COMPONENT_PAGINATOR = 'paginator';

    /** @const Nazev komponenty vyhledavani */
    const COMPONENT_SEARCH = 'search';

    /** @var ObjectService */
    protected $objectService;

    /** @var ObjectDraftService */
    protected $draftService;

    /** @var IPaginatorControlFactory */
    protected $paginatorFactory;

    /** @var IObjectSearchControlFactory */
    protected $searchFactory;

    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /**
     * @param ObjectService $objectService
     * @param ObjectDraftService $draftService
     * @param IPaginatorControlFactory $paginatorFactory
     * @param IObjectSearchControlFactory $searchFactory
     * @param ObjectRestrictorBuilder $restrictorBuilder
     */
    public function __construct(
        ObjectService $objectService,
        ObjectDraftService $draftService,
        IPaginatorControlFactory $paginatorFactory,
        IObjectSearchControlFactory $searchFactory,
        ObjectRestrictorBuilder $restrictorBuilder
    )
    {
        $this->objectService = $objectService;
        $this->draftService = $draftService;
        $this->paginatorFactory = $paginatorFactory;
        $this->searchFactory = $searchFactory;
        $this->restrictorBuilder = $restrictorBuilder;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->drafts = $this->prepareDrafts();
        $template->objects = $this->prepareObjects();
        $template->actions = $this->prepareActions();

        $template->draftMapObjectIds = [];

        foreach ($template->drafts as $draft) {
            $template->draftMapObjectIds[] = $draft['map_object_object_id'];
        }

        $template->render();
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleDeleteDraft($id)
    {
        if ($this->getPresenter()->getUser()->isAllowed(Authorizator::RESOURCE_DRAFT, Authorizator::ACTION_DELETE)) {
            if ($draft = $this->draftService->getDraft($id)) {
                $this->draftService->removeDraft($id);

                $this->redrawControl('drafts');
                $this->redrawControl('objects');
            } else {
                throw new \Nette\Application\BadRequestException;
            }
        }
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleDeleteObject($id)
    {
        if ($this->getPresenter()->getUser()->isAllowed(Authorizator::RESOURCE_OBJECT, Authorizator::ACTION_DELETE)) {
            if ($object = $this->objectService->getObject($id)) {
                $this->getPresenter()->checkOwnership($object);

                $this->objectService->removeObject($object);

                $this->redrawControl('objects');
                $this->redrawControl('drafts');
            } else {
                throw new \Nette\Application\BadRequestException;
            }
        }
    }

    /**
     * @return array
     */
    protected function prepareDrafts()
    {
        $drafts = $this->draftService->getDraftsByUser($this->getPresenter()->getUser());

        return $drafts;
    }

    /**
     * @return array
     */
    protected function prepareObjects()
    {
        $restrictor = $this->restrictorBuilder->getRestrictor($this[self::COMPONENT_SEARCH]->getParameters());

        /** @var Paginator $paginator */
        $paginator = $this[self::COMPONENT_PAGINATOR]->getPaginator();
        $paginator->setItemCount($this->objectService->getObjectsCount($restrictor));

        $objects = $this->objectService->getObjects($restrictor, ['title' => IMapper::ORDER_ASC], $paginator);

        return $objects;
    }

    /**
     * @return array
     */
    protected function prepareActions()
    {
        $actions = [
            Authorizator::ACTION_EDIT, Authorizator::ACTION_JOIN, Authorizator::ACTION_DELETE,
            Authorizator::ACTION_HISTORY, Authorizator::ACTION_OWNER
        ];

        $user = $this->getPresenter()->getUser();

        foreach ($actions as $key => $action) {
            if (!$user->isAllowed(Authorizator::RESOURCE_OBJECT, $action)) {
                unset($actions[$key]);
            }
        }

        return array_flip($actions);
    }

    /**
     * @return PaginatorControl
     */
    protected function createComponentPaginator()
    {
        $control = $this->paginatorFactory->create();
        $control->onPageChange[] = function() {
            $this->redrawControl('objects');
        };

        return $control;
    }

    /**
     * @return ObjectSearchControl
     */
    protected function createComponentSearch()
    {
        $control = $this->searchFactory->create();
        $control->onFilterChange[] = function($values) {
            $this[self::COMPONENT_PAGINATOR]->setPage(null);
        };

        return $control;
    }
}
