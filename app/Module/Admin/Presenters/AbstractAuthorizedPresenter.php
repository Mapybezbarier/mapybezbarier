<?php

namespace MP\Module\Admin\Presenters;

use MP\Module\Admin\Component\DashboardControl\DashboardControl;
use MP\Module\Admin\Component\DashboardControl\IDashboardControlFactory;
use MP\Module\Admin\Service\Authorizator;
use MP\Util\Arrays;
use Nette\Security\IAuthorizator;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractAuthorizedPresenter extends AbstractAdminPresenter
{
    /** @var IAuthorizator @inject */
    public $authorizator;

    protected function startup()
    {
        parent::startup();

        $user = $this->getUser();

        if (!$this->userService->getUser($user->getId())) {
            $user->logout(true);
        }

        if (!$user->isLoggedIn()) {
            $this->redirect(':Admin:Access:login', [AccessPresenter::PARAM_RESTORE => $this->storeRequest()]);
        } else {
            $user->setAuthorizator($this->authorizator);
        }

        $this->checkAccess();
    }

    /**
     * Overi pristup ke zdroji na zaklade ACL.
     *
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function checkAccess()
    {
        $parts = explode(':', $this->getName());

        $resource = end($parts);
        $action = $this->getAction();

        if (!$this->getUser()->isAllowed($resource, $action)) {
            throw new \Nette\Application\ForbiddenRequestException("Action '{$action}' is not allowed on resource '{$resource}' for the current user");
        }
    }

    /**
     * Overi, zda je uzivatel vlastnik zaznamu, jinak je mu odepren pristup.
     *
     * @param array $entry
     * @param string $column
     *
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function checkOwnership(array $entry, $column = 'user_id')
    {
        $owner = true;

        if ($id = Arrays::get($entry, $column, null)) {
            $user = $this->getUser();

            if ($user->isInRole(Authorizator::ROLE_AGENCY)) {
                $owner = $this->checkAgencyOwnership($id);
            } else if ($user->isInRole(Authorizator::ROLE_MAPPER)) {
                $owner = $this->checkMapperOwnership($id);
            }
        }

        if (false === $owner) {
            throw new \Nette\Application\ForbiddenRequestException("User is not the owner of the entry");
        }
    }

    /**
     * @param IDashboardControlFactory $factory
     *
     * @return DashboardControl
     */
    protected function createComponentDashboard(IDashboardControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * Overi, zda je uzivatel v roli agentury vlastnik zaznamu
     *
     * @param int $id
     *
     * @return bool
     */
    protected function checkAgencyOwnership($id)
    {
        $isOwnedByMyself = ($id == $this->getUser()->getId());
        $agencyMappersIds = Arrays::pairs($this->userService->getMappers($this->getUser()->getId()), 'id', 'id');
        $isOwnedByMyMapper = in_array($id, $agencyMappersIds, true);

        $owner = ($isOwnedByMyself || $isOwnedByMyMapper);

        return $owner;
    }

    /**
     * Overi, zda je uzivatel v roli mapare vlastnik zaznamu
     *
     * @param int $id
     *
     * @return bool
     */
    protected function checkMapperOwnership($id)
    {
        $isOwnedByMyself = ($id == $this->getUser()->getId());

        return $isOwnedByMyself;
    }
}
