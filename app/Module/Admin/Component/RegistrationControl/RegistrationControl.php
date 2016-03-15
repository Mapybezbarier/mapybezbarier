<?php

namespace MP\Module\Admin\Component\RegistrationControl;

use Kdyby\Translation\Translator;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Component\AbstractUserControl;
use MP\Module\Admin\Manager\LicenseManager;
use MP\Module\Admin\Service\Authenticator;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\UserService;
use Nette\Application\UI\Form;

/**
 * Komponenta pro vykresleni registracniho formulare.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class RegistrationControl extends AbstractUserControl
{
    /** @const Nazvy komponent */
    const COMPONENT_FORM = 'form',
        COMPONENT_ROLE = 'role_id',
        COMPONENT_FIRSTNAME = 'firstname',
        COMPONENT_SURNAME = 'surname';

    /** @var Authenticator */
    protected $authenticator;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     * @param Translator $translator
     * @param LicenseManager $licenseManager
     * @param Authenticator $authenticator
     */
    public function __construct(
        FormFactory $factory,
        UserService $userService,
        Translator $translator,
        LicenseManager $licenseManager,
        Authenticator $authenticator
    ) {
        parent::__construct($factory, $userService, $translator, $licenseManager);

        $this->authenticator = $authenticator;
    }

    /**
     * @param Form $form
     */
    protected function appendControls(Form $form)
    {
        $form->addText(self::COMPONENT_FIRSTNAME, 'backend.control.user.label.firstname', 255)->setRequired(true);
        $form->addText(self::COMPONENT_SURNAME, 'backend.control.user.label.surname', 255)->setRequired(true);

        $this->appendCommonRoleControls($form);

        $this->appendLicenseControls($form, true);

        $form[self::COMPONENT_PHONE]->setRequired(false);

        $form->addHidden(self::COMPONENT_ROLE, $this->prepareRole());
        $form->addSubmit('submit', 'backend.user.action.register');
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = array_filter($form->getValues(true));

        $user = $this->userService->createUser($values, $this->getPresenter()->getUser());

        $this->getPresenter()->getUser()->login($this->authenticator->getIdentity($user));

        $this->getPresenter()->redirect(':Admin:Dashboard:default');
    }

    /**
     * @return int
     */
    protected function prepareRole()
    {
        $role = $this->userService->getRole(Authorizator::ROLE_MAPPER);

        if (!$role) {
            throw new \Nette\InvalidStateException("Role '" . Authorizator::ROLE_MAPPER . "' is missing.");
        }

        return $role['id'];
    }
}
