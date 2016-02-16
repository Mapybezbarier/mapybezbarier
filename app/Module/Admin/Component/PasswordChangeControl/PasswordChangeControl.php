<?php

namespace MP\Module\Admin\Component\PasswordChangeControl;

use Kdyby\Translation\Translator;
use MP\Component\FlashMessageControl;
use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Service\Authenticator;
use MP\Module\Admin\Service\UserService;
use MP\Util\Arrays;
use Nette\Application\UI\Form;
use Nette\Security\IAuthenticator;

/**
 * Komponenta pro zmenu hesla.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class PasswordChangeControl extends AbstractFormControl
{
    /** @const Nazev komponenty s aktualnim heslem. */
    const COMPONENT_CURRENT_PASSWORD = 'current_password';

    /** @const Nazev komponenty s novym heslem. */
    const COMPONENT_NEW_PASSWORD = 'new_password';

    /** @var UserService */
    protected $userService;

    /** @var Authenticator */
    protected $authenticator;

    /** @var Translator */
    protected $translator;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     * @param Authenticator $authenticator
     * @param Translator $translator
     */
    public function __construct(FormFactory $factory, UserService $userService, Authenticator $authenticator, Translator $translator)
    {
        parent::__construct($factory);

        $this->userService = $userService;
        $this->authenticator = $authenticator;
        $this->translator = $translator;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }

    /**
     * @param Form $form
     */
    public function changePassword(Form $form)
    {
        $values = $form->getValues(true);

        $this->userService->changeUserPassword($this->getPresenter()->getUser(), $values[self::COMPONENT_NEW_PASSWORD]);

        $this->flashMessage('backend.control.passwordChange.flash.success.change', FlashMessageControl::TYPE_SUCCESS);

        $this->getPresenter()->redirect(':Admin:User:default');
    }

    /**
     * @param Form $form
     */
    public function validateCurrentPassword(Form $form)
    {
        $values = $form->getValues(true);

        try {
            $login = Arrays::get($this->getPresenter()->getUser()->getIdentity()->getData(), 'login');
            $password = Arrays::get($values, self::COMPONENT_CURRENT_PASSWORD, null);

            $this->authenticator->authenticate([$login, $password]);
        } catch (\Nette\Security\AuthenticationException $e) {
            if (IAuthenticator::INVALID_CREDENTIAL === $e->getCode()) {
                $form->addError($this->translator->translate('backend.control.passwordChange.error.invalidCurrentPassword'));
            } else {
                $form->addError($this->translator->translate('backend.control.passwordChange.error.failure'));
            }
        }
    }

    /**
     * @param string $name
     *
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = $this->factory->create($this, $name);
        $form->onSuccess[] = [$this, 'changePassword'];

        if (null === Arrays::get($this->getPresenter()->getUser()->getIdentity()->getData(), 'password_reset_token', null)) {
            $form->addPassword(self::COMPONENT_CURRENT_PASSWORD, 'backend.control.passwordChange.label.currentPassword')->setRequired(true);

            $form->onValidate[] = [$this, 'validateCurrentPassword'];
        }

        $form->addPassword(self::COMPONENT_NEW_PASSWORD, 'backend.control.passwordChange.label.newPassword')->setRequired(true);
        $form->addSubmit('submit', 'backend.user.action.changePassword');

        return $form;
    }
}
