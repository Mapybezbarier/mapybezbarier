<?php

namespace MP\Module\Admin\Presenters;

use MP\Module\Admin\Service\AccessService;
use MP\Module\Admin\Service\UserService;
use MP\Presenters\AbstractPresenter;

/**
 * Predek presenteru v administraci.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractAdminPresenter extends AbstractPresenter
{
    /**
     * @persistent
     * @var string
     */
    public $locale = 'cs';

    /** @var AccessService @inject */
    public $accessService;

    /** @var UserService @inject */
    public $userService;

    /**
     * Handler odhlaseni uzivatele.
     */
    public function handleLogout()
    {
        $this->logout();
    }

    /**
     * @override Nastaveni jmena prihlaseneho uzivatele.
     */
    protected function beforeRender()
    {
        parent::beforeRender();

        if ($this->getUser()->isLoggedIn()) {
            $user = $this->userService->getUser($this->getUser()->getId());

            $this->template->username = $user['fullname'];
        }
    }

    /**
     * Odhlasi uzivatele
     */
    protected function logout()
    {
        $this->accessService->logout();

        $this->redirect(':Admin:Access:login');
    }
}
