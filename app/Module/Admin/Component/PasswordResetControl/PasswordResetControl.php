<?php

namespace MP\Module\Admin\Component\PasswordResetControl;

use Kdyby\Translation\Translator;
use MP\Component\FlashMessageControl;
use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Component\Mailer\IMessageFactory;
use MP\Module\Admin\Component\PasswordResetMailer\PasswordResetMailer;
use MP\Module\Admin\Service\Authenticator;
use MP\Module\Admin\Service\UserService;
use MP\Util\Strings;
use Nette\Application\UI\Form;

/**
 * Komponenta pro obnoveni hesla.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class PasswordResetControl extends AbstractFormControl
{
    /** @const Nazvy komponent */
    const COMPONENT_FORM = 'form',
        COMPONENT_EMAIL = 'email';

    /** @var UserService */
    protected $userService;

    /** @var PasswordResetMailer */
    protected $resetMailer;

    /** @var Authenticator */
    protected $authenticator;

    /** @var array */
    protected $user;

    /** @var Translator */
    protected $translator;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     * @param PasswordResetMailer $resetMailer
     * @param Authenticator $authenticator
     * @param Translator $translator
     */
    public function __construct(
        FormFactory $factory,
        UserService $userService,
        PasswordResetMailer $resetMailer,
        Authenticator $authenticator,
        Translator $translator
    )
    {
        parent::__construct($factory);

        $this->userService = $userService;
        $this->resetMailer = $resetMailer;
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
    public function sendResetLink(Form $form)
    {
        $values = $form->getValues(true);

        $token = $this->userService->createPasswordResetToken($values);

        $this->resetMailer->send([
            IMessageFactory::TO => $values[self::COMPONENT_EMAIL],
            IMessageFactory::DATA => [
                'link' => $this->link('//change!', ['token' => $token]),
            ],
            IMessageFactory::SUBJECT => 'backend.control.passwordReset.mail.subject'
        ]);

        $this->flashMessage('backend.control.passwordReset.flash.success.sent', FlashMessageControl::TYPE_SUCCESS);

        $this->getPresenter()->redirect(':Admin:Access:login');
    }

    /**
     * @param string $token
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleChange($token)
    {
        $valid = $this->userService->validatePasswordResetToken($token);

        if ($valid) {
            $user = $this->getUserByToken($token);

            $this->getPresenter()->getUser()->login($this->authenticator->getIdentity($user));
            $this->getPresenter()->redirect(':Admin:Access:change');
        } else {
            $this->flashMessage('backend.control.passwordReset.flash.error.validToken', FlashMessageControl::TYPE_INFO);

            $this->getPresenter()->redirect(':Admin:Access:reset');
        }
    }

    /**
     * @param Form $form
     */
    public function checkEmail(Form $form)
    {
        $values = $form->getValues(true);

        $user = $this->getUserByEmail($values[self::COMPONENT_EMAIL]);

        if (!$user) {
            $form->addError($this->translator->translate('backend.control.passwordReset.error.unknownEmail'));
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
        $form->onSuccess[] = [$this, 'sendResetLink'];
        $form->onValidate[] = [$this, 'checkEmail'];

        $form->addText(self::COMPONENT_EMAIL, 'backend.control.passwordReset.label.email')->setRequired(true)->addRule(Form::EMAIL);
        $form->addSubmit('submit', 'backend.user.action.resetPassword');

        return $form;
    }

    /**
     * @param string $email
     *
     * @return array|null
     */
    protected function getUserByEmail($email)
    {
        $email = Strings::lower($email);
        $email = Strings::trim($email);

        return $this->getUser([["[email] = %s", $email]]);
    }

    /**
     * @param string $token
     *
     * @return array|null
     */
    protected function getUserByToken($token)
    {
        return $this->getUser([["[password_reset_token] = %s", $token]]);
    }

    /**
     * @param array $restrictor
     *
     * @return array|null
     */
    protected function getUser(array $restrictor)
    {
        if (!isset($this->user)) {
            $this->user = $this->userService->getUserBy($restrictor);
        }

        return $this->user;
    }
}
