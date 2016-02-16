<?php

namespace MP\Module\Admin\Component;

use Kdyby\Translation\Translator;
use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Manager\LicenseManager;
use MP\Module\Admin\Service\UserService;
use MP\Util\Forms;
use MP\Util\Strings;
use Nette\Application\UI\Form;

/**
 * Predek komponent pro praci s uzivatelem, napr. editace, vytvoreni, registrace.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractUserControl extends AbstractFormControl
{
    /** @const Nazvy komponent */
    const COMPONENT_EMAIL = 'email',
        COMPONENT_PHONE = 'phone',
        COMPONENT_LOGIN = 'login',
        COMPONENT_PASSWORD = 'password',
        COMPONENT_LICENSE = 'license_id';

    /** @var UserService */
    protected $userService;

    /** @var Translator */
    protected $translator;

    /** @var LicenseManager */
    protected $licenseManager;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     * @param Translator $translator
     * @param LicenseManager $licenseManager
     */
    public function __construct(FormFactory $factory, UserService $userService, Translator $translator, LicenseManager $licenseManager)
    {
        parent::__construct($factory);

        $this->userService = $userService;
        $this->translator = $translator;
        $this->licenseManager = $licenseManager;
    }

    /**
     * @param Form $form
     */
    abstract protected function appendControls(Form $form);

    /**
     * @param Form $form
     */
    abstract public function processForm(Form $form);

    /**
     * Vykresleni komponenty.
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }

    /**
     * @param Form $form
     */
    public function checkConstraints(Form $form)
    {
        if (null !== $this->checkEmailContraint($form)) {
            $form->addError($this->translator->translate('backend.control.user.error.emailAlreadyTaken'));
        }

        if (null !== $this->checkLoginContraint($form)) {
            $form->addError($this->translator->translate('backend.control.user.error.loginAlreadyTaken'));
        }
    }

    /**
     * @param Form $form
     *
     * @return array|null
     */
    protected function checkEmailContraint(Form $form)
    {
        $values = $form->getValues(true);

        $email = Strings::lower($values[self::COMPONENT_EMAIL]);
        $email = Strings::trim($email);

        $user = $this->userService->getUserBy([["[email] = %s", $email]]);

        return $user;
    }

    /**
     * @param Form $form
     *
     * @return array|null
     */
    protected function checkLoginContraint(Form $form)
    {
        $values = $form->getValues(true);

        $login = Strings::lower($values[self::COMPONENT_LOGIN]);
        $login = Strings::trim($login);

        $user = $this->userService->getUserBy([["[login] = %s", $login]]);

        return $user;
    }

    /**
     * @param string $name
     *
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = $this->factory->create($this, $name);
        $form->onSuccess[] = [$this, 'processForm'];
        $form->onValidate[] = [$this, 'checkConstraints'];

        $this->appendControls($form);

        return $form;
    }

    /**
     * @param Form $form
     */
    protected function appendCommonRoleControls(Form $form)
    {
        $form->addText(self::COMPONENT_EMAIL, 'backend.control.user.label.email', 255)
            ->setType('email')
            ->setRequired(true)
            ->addRule(Form::EMAIL);
        $form->addText(self::COMPONENT_PHONE, 'backend.control.user.label.phone', 255)
            ->setType('tel')
            ->setRequired(true);
        $form->addText(self::COMPONENT_LOGIN, 'backend.control.user.label.login', 255)->setRequired(true);
        $form->addPassword(self::COMPONENT_PASSWORD, 'backend.control.user.label.password', 255)->setRequired(true);
    }

    /**
     * @param Form $form
     * @param bool $default
     */
    protected function appendLicenseControls(Form $form, $default = false)
    {
        $values = $this->licenseManager->findAll() ?: [];
        $values = Forms::toSelect($values, 'id', 'title');

        if ($default) {
            $values = [null => 'backend.control.user.value.license.default'] + $values;
        }

        $form->addSelect(self::COMPONENT_LICENSE, 'backend.control.user.label.license', $values)
            ->setRequired(false === $default);
    }
}
