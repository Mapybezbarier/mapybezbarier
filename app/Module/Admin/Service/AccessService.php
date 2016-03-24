<?php

namespace MP\Module\Admin\Service;

use Kdyby\Translation\Translator;
use MP\Manager\UserManager;
use Nette\Http\Request;
use Nette\Security\IAuthenticator;
use Nette\Security\User;
use Nette\Utils\Json;
use Tracy\Debugger;

/**
 * Sluzba pro prihlasovani/odhlasovani uzivatelu.
 */
class AccessService
{
    /** @var User */
    protected $user;

    /** @var Request */
    protected $request;

    /** @var LogService */
    protected $logService;

    /** @var UserManager */
    protected $userManager;

    /**  @var Translator */
    protected $translator;

    /**
     * @param User $user
     * @param Request $request
     * @param LogService $logService
     * @param UserManager $userManager
     * @param Translator $translator
     */
    public function __construct(
        User $user,
        Request $request,
        LogService $logService,
        UserManager $userManager,
        Translator $translator
    ) {
        $this->user = $user;
        $this->request = $request;
        $this->logService = $logService;
        $this->userManager = $userManager;
        $this->translator = $translator;

        $this->bindEvents();
    }

    /**
     * Prihlasnei uzivatele
     * @param string $login
     * @param string $password
     * @param bool $permanent
     *
     * @return null|string
     */
    public function login($login, $password, $permanent)
    {
        $errorMessage = null;

        try {
            $this->user->login($login, $password);
        } catch (\Nette\Security\AuthenticationException $e) {
            switch ($e->getCode()) {
            case IAuthenticator::IDENTITY_NOT_FOUND:
                $errorMessage = $this->translator->translate('backend.control.login.error.indentityNotFound', ['login' => $login]);
                break;

            case IAuthenticator::INVALID_CREDENTIAL:
                $errorMessage = $this->translator->translate('backend.control.login.error.invalidCredentials');
                break;

            default:
                $errorMessage = $this->translator->translate('backend.control.login.error.failure');

                Debugger::log($e, Debugger::WARNING);
            }
        }

        if (null === $errorMessage) {
            $this->user->setExpiration($permanent ? 0 : '30 minutes', (false === $permanent), true);
        }

        return $errorMessage;
    }

    /**
     * Odhlaseni uzivatele
     */
    public function logout()
    {
        $this->user->logout(true);
    }

    /**
     * Na udalost prihlaseni a odhlaseni navesim logovani
     */
    protected function bindEvents()
    {
        $this->user->onLoggedIn[] = function(User $user) {
            if ($this->userManager->findOneById($user->getId())) {
                $loginData = [
                    'ip' => $this->request->getRemoteAddress(),
                    'ua' => $this->request->getHeader('User-Agent'),
                ];

                $this->logService->log(
                    Authorizator::RESOURCE_USER, LogService::ACTION_USER_LOGIN,
                    $user->getId(), null, Json::encode($loginData), $user->getId()
                );
            }
        };

        $this->user->onLoggedOut[] = function(User $user) {
            if ($this->userManager->findOneById($user->getId())) {
                $this->logService->log(
                    Authorizator::RESOURCE_USER, LogService::ACTION_USER_LOGOUT,
                    $user->getId(), null, null, $user->getId()
                );
            }
        };
    }
}
