<?php

namespace MP\Module\Admin\Component\UserList;

use MP\Component\AbstractControl;
use MP\Module\Admin\Component\PaginatorControl\IPaginatorControlFactory;
use MP\Module\Admin\Component\PaginatorControl\PaginatorControl;
use MP\Module\Admin\Component\UserSearchControl\IUserSearchControlFactory;
use MP\Module\Admin\Component\UserSearchControl\UserSearchControl;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\UserService;
use Nette\Utils\Paginator;

/**
 * Komponent pro vykresleni seznamu uzivatelu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class UserListControl extends AbstractControl
{
    /** @const Nazev komponenty paginatoru */
    const COMPONENT_PAGINATOR = 'paginator';

    /** @const Nazev komponenty vyhledavani */
    const COMPONENT_SEARCH = 'search';

    /** @var UserService */
    protected $userService;
    /**
     * @var IPaginatorControlFactory
     */
    protected $paginatorFactory;
    /**
     * @var IUserSearchControlFactory
     */
    protected $searchFactory;

    /**
     * @param UserService $userService
     * @param IPaginatorControlFactory $paginatorFactory
     * @param IUserSearchControlFactory $searchFactory
     */
    public function __construct(UserService $userService, IPaginatorControlFactory $paginatorFactory, IUserSearchControlFactory $searchFactory)
    {
        $this->userService = $userService;
        $this->paginatorFactory = $paginatorFactory;
        $this->searchFactory = $searchFactory;
    }

    public function render()
    {
        $template = $this->getTemplate();

        $user = $this->getPresenter()->getUser();

        $restrictions = $this[self::COMPONENT_SEARCH]->getParameters();

        /** @var Paginator $paginator */
        $paginator = $this[self::COMPONENT_PAGINATOR]->getPaginator();
        $paginator->setItemCount($this->userService->getUsersCount($user, $restrictions));

        $template->users = $this->userService->getUsers($user, $restrictions, false, $paginator);
        $template->cols = ['name', 'login', 'role', 'edit'];

        if ($user->isAllowed(Authorizator::RESOURCE_USER, Authorizator::ACTION_DELETE)) {
            $template->cols[] = 'delete';
        }

        if ($user->isInRole(Authorizator::ROLE_MASTER) || $user->isInRole(Authorizator::ROLE_ADMIN)) {
            $template->showSearch = true;
        }

        $template->render();
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function handleDelete($id)
    {
        if ($this->getPresenter()->getUser()->isAllowed(Authorizator::RESOURCE_USER, Authorizator::ACTION_DELETE)) {
            $user = $this->userService->getUser($id);

            if ($user) {
                $this->userService->deleteUser($id);
            } else {
                throw new \Nette\Application\BadRequestException;
            }

            $this->redrawControl('users');
        } else {
            throw new \Nette\Application\ForbiddenRequestException;
        }
    }

    /**
     * @return PaginatorControl
     */
    protected function createComponentPaginator()
    {
        $control = $this->paginatorFactory->create();
        $control->onPageChange[] = function() {
            $this->redrawControl('users');
        };

        return $control;
    }

    /**
     * @return UserSearchControl
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
