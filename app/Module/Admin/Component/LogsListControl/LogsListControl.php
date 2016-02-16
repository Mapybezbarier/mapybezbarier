<?php

namespace MP\Module\Admin\Component\LogsList;

use MP\Component\AbstractControl;
use MP\Module\Admin\Component\LogSearchControl\ILogSearchControlFactory;
use MP\Module\Admin\Component\LogSearchControl\LogSearchControl;
use MP\Module\Admin\Component\PaginatorControl\IPaginatorControlFactory;
use MP\Module\Admin\Component\PaginatorControl\PaginatorControl;
use MP\Module\Admin\Service\LogService;
use Nette\Utils\Paginator;

/**
 * Komponenta pro vykresleni seznamu logu aktivit uzivatelu systemu.
 * Vybrane zalogovane akce maji take detail
 */
class LogsListControl extends AbstractControl
{
    /** @const Nazev komponenty paginatoru */
    const COMPONENT_PAGINATOR = 'paginator';

    /** @const Nazev komponenty vyhledavani */
    const COMPONENT_SEARCH = 'search';

    /** @var LogService */
    protected $service;

    /** @var IPaginatorControlFactory */
    protected $paginatorFactory;

    /** @var ILogSearchControlFactory */
    protected $searchFactory;

    /**
     * @param LogService $service
     * @param IPaginatorControlFactory $paginatorFactory
     * @param ILogSearchControlFactory $searchFactory
     */
    public function __construct(
        LogService $service,
        IPaginatorControlFactory $paginatorFactory,
        ILogSearchControlFactory $searchFactory
    ) {
        $this->service = $service;
        $this->paginatorFactory = $paginatorFactory;
        $this->searchFactory = $searchFactory;
    }

    public function render()
    {
        $template = $this->getTemplate();

        $restrictions = $this[self::COMPONENT_SEARCH]->getParameters();

        /** @var Paginator $paginator */
        $paginator = $this[self::COMPONENT_PAGINATOR]->getPaginator();
        $paginator->setItemCount($this->service->getListCount($restrictions));

        $template->items = $this->service->findListData($restrictions, $paginator);
        $template->render();
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function renderDetail($id)
    {
        $params = $this->service->getDetailParams($id);

        if (empty($params['type'])) {
            throw new \Nette\Application\ForbiddenRequestException;
        } else {
            $template = $this->getTemplate($params['type']);
            $template->setParameters($params);
        }

        $template->render();
    }

    /**
     * @return PaginatorControl
     */
    protected function createComponentPaginator()
    {
        $control = $this->paginatorFactory->create();
        $control->onPageChange[] = function() {
            $this->redrawControl('list');
        };

        return $control;
    }

    /**
     * @return LogSearchControl
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
