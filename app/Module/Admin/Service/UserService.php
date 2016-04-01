<?php

namespace MP\Module\Admin\Service;

use MP\Manager\RoleManager;
use MP\Manager\UserManager;
use MP\Mapper\IMapper;
use MP\Util\Arrays;
use Nette\Caching\Cache;
use Nette\Security\Passwords;
use Nette\Security\User;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Nette\Utils\Paginator;
use Nette\Utils\Random;

/**
 * Sluzba pro spravu uzivatelu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class UserService
{
    /** @var UserManager */
    protected $userManager;

    /** @var RoleManager */
    protected $roleManager;

    /** @var LogService */
    protected $logService;

    /** @var Cache */
    protected $cache;

    /** @var Authenticator */
    protected $authenticator;
    /**
     * @var UserRestrictorBuilder
     */
    protected $restrictorBuilder;

    /**
     * @param UserManager $userManager
     * @param RoleManager $roleManager
     * @param LogService $logService
     * @param Cache $cache
     * @param Authenticator $authenticator
     * @param UserRestrictorBuilder $restrictorBuilder
     */
    public function __construct(
        UserManager $userManager,
        RoleManager $roleManager,
        LogService $logService,
        Cache $cache,
        Authenticator $authenticator,
        UserRestrictorBuilder $restrictorBuilder
    ) {
        $this->userManager = $userManager;
        $this->roleManager = $roleManager;
        $this->logService = $logService;
        $this->cache = $cache;
        $this->authenticator = $authenticator;
        $this->restrictorBuilder = $restrictorBuilder;
    }

    /**
     * @param array $values
     * @param User|null $user
     *
     * @return array
     */
    public function createUser(array $values, User $user)
    {
        $values['password'] = Passwords::hash($values['password']);

        if (null !== $user && $user->isInRole(Authorizator::ROLE_AGENCY)) {
            $values['parent_id'] = $user->getId();
        }

        $savedUser = $this->userManager->persist($values);

        $this->prepareUser($savedUser);

        $saveData = ['data' => $savedUser];
        unset($saveData['data']['password']);

        $logUserId = (null === $user || $user->isInRole(Authorizator::ROLE_GUEST))? $savedUser['id'] : null;

        $this->logService->log(
            Authorizator::RESOURCE_USER, LogService::ACTION_USER_CREATE,
            $savedUser['id'], $values['login'], Json::encode($saveData), $logUserId
        );

        return $savedUser;
    }

    /**
     * @param array $values
     * @param User $user
     *
     * @return array
     * @throws \Nette\Utils\JsonException
     */
    public function editUser(array $values, User $user)
    {
        $savedUser = $this->userManager->persist($values);

        $this->prepareUser($savedUser);

        if ($savedUser[IMapper::ID] == $user->getId()) {
            $user->getStorage()->setIdentity($this->authenticator->getIdentity($savedUser));
        }

        $saveData = ['data' => $values];

        $this->logService->log(
            Authorizator::RESOURCE_USER, LogService::ACTION_USER_EDIT,
            $savedUser['id'], null, Json::encode($saveData)
        );

        return $user;
    }

    /**
     * Smaze uzivatele.
     *
     * @param int $id
     */
    public function deleteUser($id)
     {
        $this->userManager->remove($id);
        $this->logService->log(Authorizator::RESOURCE_USER, LogService::ACTION_USER_DELETE, $id);
    }

    /**
     * @param int $id
     * @param bool $raw
     *
     * @return array|null
     */
    public function getUser($id, $raw = false)
    {
        $user = $this->userManager->findOneById($id);

        if ($user && false === $raw) {
            $this->prepareUser($user);
        }

        return $user;
    }

    /**
     * @param array $restrictor
     * @param bool $raw
     *
     * @return array|null
     */
    public function getUserBy(array $restrictor, $raw = false)
    {
        $user = $this->userManager->findOneBy($restrictor);

        if ($user && false === $raw) {
            $this->prepareUser($user);
        }

        return $user;
    }

    /**
     * @param User $user
     * @param array $restrictions
     * @param bool $raw
     * @param Paginator $paginator
     *
     * @return array
     */
    public function getUsers(User $user, $restrictions = [], $raw = true, Paginator $paginator = null)
    {
        $restrictor = $this->restrictorBuilder->getRestrictor($user, $restrictions);
        $users = $this->userManager->findAll($restrictor, null, $paginator);

        if (false === $raw) {
            foreach ($users as &$user) {
                $this->prepareUser($user);
            }
        }

        return $users;
    }

    /**
     * @param $user
     * @param array $restrictions
     *
     * @return int
     */
    public function getUsersCount(User $user, $restrictions = [])
    {
        $restrictor = $this->restrictorBuilder->getRestrictor($user, $restrictions);
        return $this->userManager->findCount($restrictor);
    }

    /**
     * @return array
     */
    public function getAgencies()
    {
        $role = $this->getRole(Authorizator::ROLE_AGENCY);

        $restrictor = [["[role_id] = %i", $role['id']]];

        $agencies = $this->userManager->findAll($restrictor);

        return $agencies;
    }

    /**
     * @param int $agency
     *
     * @return array
     */
    public function getMappers($agency)
    {
        $restrictor = [["[parent_id] = %i", $agency]];

        $mappers = $this->userManager->findAll($restrictor);

        return $mappers;
    }

    /**
     * @param string $role
     *
     * @return array|null
     */
    public function getRole($role)
    {
        $role = $this->cache->load($role, function() use ($role) {
            return $this->roleManager->findOneBy([["[title] = %s", $role]]);
        });

        return $role;
    }

    /**
     * Vrati uzivatelske role.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roleManager->findAll();
        $roles = Arrays::pairs($roles, 'id', 'title');

        return $roles;
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return array
     */
    public function changeUserPassword(User $user, $password)
    {
        $values = [
            'id' => $user->getId(),
            'password' => Passwords::hash($password),
            'password_reset_token' => null,
            'password_reset_token_time' => null,
        ];

        $ret = $this->userManager->persist($values);

        $this->logService->log(Authorizator::RESOURCE_USER, LogService::ACTION_USER_PWD_CHANGE, $user->getId());

        return $ret;
    }

    /**
     * Vytvori token pro reset hesla.
     *
     * @param array $values
     *
     * @return string
     */
    public function createPasswordResetToken(array $values)
    {
        $token = Random::generate(255);
        $validity = DateTime::from(time())->modify('+1 hour');

        $user = $this->getUserBy([["[email] = %s", $values['email']]]);

        $values = [
            'id' => $user['id'],
            'password_reset_token' => $token,
            'password_reset_token_time' => $validity,
        ];

        $this->userManager->persist($values);

        $this->logService->log(
            Authorizator::RESOURCE_USER, LogService::ACTION_USER_PWD_RESET,
            $user['id'], null, null, $user['id']
        );

        return $token;
    }

    /**
     * Overi token pro reset hesla.
     *
     * @param string $token
     *
     * @return bool
     */
    public function validatePasswordResetToken($token)
    {
        if ($token) {
            $user = $this->getUserBy([["[password_reset_token] = %s", $token]]);

            if ($user) {
                $valid = ($user['password_reset_token_time']->getTimestamp() > time());
            } else {
                $valid = false;
            }
        } else {
            $valid = false;
        }

        return $valid;
    }

    /**
     * @param array $user
     */
    protected function prepareUser(array &$user)
    {
        if ($user['role_id'] == $this->getRole(Authorizator::ROLE_AGENCY)['id']) {
            $user['fullname'] = $user['ic_title'];
        } else {
            $user['fullname'] = "{$user['firstname']} {$user['surname']}";
        }

        if ($user['role_id'] == $this->getRole(Authorizator::ROLE_MAPPER)) {
            if ($user['parent_id'] && null === $user['license_id']) {
                $user['license_id'] = $this->getUser($user['parent_id'])['license_id'];
            }
        }
    }

    /**
     * Vrati, zda je uzivatel certifikovany
     *
     * @param int $id
     *
     * @return bool
     */
    public function isCertified($id)
    {
        $user = $this->getUser($id);

        return (bool) Arrays::get($user, 'certified', false);
    }
}
