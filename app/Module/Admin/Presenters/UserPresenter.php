<?php

namespace MP\Module\Admin\Presenters;

use MP\Mapper\IMapper;
use MP\Module\Admin\Component\UserControl\IUserControlFactory;
use MP\Module\Admin\Component\UserControl\UserControl;
use MP\Module\Admin\Component\UserList\IUserListControlFactory;
use MP\Module\Admin\Component\UserList\UserListControl;
use MP\Module\Admin\Service\UserService;

/**
 * Sprava uzivatelu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class UserPresenter extends AbstractAuthorizedPresenter
{
    /** @const Nazev komponenty uzivatele. */
    const COMPONENT_USER = 'user';

    /** @var UserService @inject */
    public $userService;

    public function actionCreate()
    {
        $this->setView('user');
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
    {
        $user = $this->userService->getUser($id);

        if ($user) {
            $this->checkOwnership($user, IMapper::ID);

            $this->setView('user');

            $this[self::COMPONENT_USER]->setId($id);
        } else {
            throw new \Nette\Application\BadRequestException("Unknow user with ID '{$id}'");
        }
    }

    /**
     * @param IUserListControlFactory $factory
     *
     * @return UserListControl
     */
    protected function createComponentUserList(IUserListControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IUserControlFactory $factory
     *
     * @return UserControl
     */
    protected function createComponentUser(IUserControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
