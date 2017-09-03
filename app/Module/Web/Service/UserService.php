<?php

namespace MP\Module\Web\Service;

use MP\Manager\RoleManager;
use MP\Manager\UserManager;
use MP\Module\Admin\Service\Authorizator;

/**
 * Sluzba pro praci s uzivateli z webu.
 */
class UserService
{
    /** @var UserManager */
    protected $userManager;

    /** @var RoleManager */
    protected $roleManager;

    /**
     * @param UserManager $userManager
     * @param RoleManager $roleManager
     */
    public function __construct(UserManager $userManager, RoleManager $roleManager)
    {
        $this->userManager = $userManager;
        $this->roleManager = $roleManager;
    }


    /**
     * Vrati jmeno uzivetele pro ucely zobrazeni nazvu zdroje
     * pouze pro roli agentura a mapar
     * @param int $id
     * $return string|null
     */
    public function getSourceName($id)
    {
        $ret = null;

        $user = $this->userManager->findOneById($id);

        if ($user['parent_id']) {
            $agency = $this->userManager->findOneById($user['parent_id']);
            $ret = $agency['ic_title'];
        } elseif ($this->getRole(Authorizator::ROLE_MAPPER)['id'] === $user['role_id']) {
            $ret = "{$user['firstname']} {$user['surname']}";
        }

        return $ret;
    }

    /**
     * @param string $role
     *
     * @return array|null
     */
    public function getRole($role)
    {
        return $this->roleManager->findOneBy([["[title] = %s", $role]]);
    }
}
