<?php

namespace MP\Module\Admin\Service;

use Dibi\Row;
use MP\Manager\RoleManager;
use MP\Manager\UserManager;
use MP\Mapper\IMapper;
use MP\Util\Arrays;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;

/**
 * Sluzba pro autentizaci uzivatelu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Authenticator implements IAuthenticator
{
    /** @const Pocet neplatnych pokusu prihlaseni. */
    const INVALID_LOGIN_COUNT = 5;
    /** @const Casovy rozestup mezi neplatnymi pokusy. */
    const INVALID_LOGIN_TIME_MARGIN = 60;

    /** @var UserManager*/
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
     * @param array $credentials
     *
     * @return IIdentity
     * @throws \Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;

        $user = $this->userManager->findOneBy([["[login] = %s", $login]]);

        if (!$user) {
            throw new \Nette\Security\AuthenticationException("User '{$login}' not found.", IAuthenticator::IDENTITY_NOT_FOUND);
        }
        if ($this->checkLoginAttempt($user)) {
            if (Passwords::verify($password, $user['password'])) {
                $user = $this->markSuccessfulAttempt($user);
            } else {
                $this->markInvalidAttempt($user);

                throw new \Nette\Security\AuthenticationException('Invalid password.', IAuthenticator::INVALID_CREDENTIAL);
            }
        } else {
            throw new \Nette\Security\AuthenticationException('Invalid login attempt exceeded.', IAuthenticator::FAILURE);
        }

        $identity = $this->getIdentity($user);

        return $identity;
    }

    /**
     * @param array $user
     *
     * @return Identity
     */
    public function getIdentity(array $user)
    {
        $roles = $this->roleManager->findAll() ?: [];
        $roles = Arrays::pairs($roles, 'id', 'title');

        $role = Arrays::get($roles, $user['role_id'], Authorizator::ROLE_GUEST);

        $user = $this->userManager->findOneById($user[IMapper::ID]);

        unset($user['password']);

        $identity = new Identity($user['id'], $role, $user);

        return $identity;
    }

    /**
     * @param Row $user
     *
     * @return bool
     */
    protected function checkLoginAttempt($user)
    {
        $attemptCountNotExceeded = $user['invalid_login_count'] < self::INVALID_LOGIN_COUNT;

        $lastAttemptWasInvalid = (null !== $user['last_invalid_login_timestamp']);
        $lastAttemptIsInMargin = (
            $lastAttemptWasInvalid
            && (($user['last_invalid_login_timestamp']->getTimestamp() + self::INVALID_LOGIN_TIME_MARGIN) < time())
        );

        $checked = ($attemptCountNotExceeded || $lastAttemptIsInMargin);

        return $checked;
    }

    /**
     * @param Row $user
     *
     * @return array|Row
     */
    protected function markSuccessfulAttempt($user)
    {
        $values = [
            'id' => $user['id'],
            'invalid_login_count' => null,
            'last_invalid_login_timestamp' => null,
            'password_reset_token' => null,
            'password_reset_token_time' => null
        ];

        $this->userManager->persist($values);

        $user = array_merge($user, $values);

        return $user;
    }

    /**
     * @param Row $user
     */
    protected function markInvalidAttempt($user)
    {
        $values = [
            'id' => $user['id'],
            'invalid_login_count' => $user['invalid_login_count'] + 1,
            'last_invalid_login_timestamp' => DateTime::from(time())
        ];

        $this->userManager->persist($values);
    }
}
