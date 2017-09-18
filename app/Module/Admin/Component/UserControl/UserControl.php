<?php

namespace MP\Module\Admin\Component\UserControl;

use h4kuna\Ares\Ares;
use MP\Module\Admin\Component\AbstractUserControl;
use MP\Manager\LicenseManager;
use MP\Module\Admin\Service\Authorizator;
use MP\Util\Arrays;
use MP\Util\Forms;
use MP\Util\Strings;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;

/**
 * Komponenta pro formular pro zalozeni/editaci uzivatele.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class UserControl extends AbstractUserControl
{
    /** @const Nazvy komponent */
    const COMPONENT_ROLE = 'role_id',
        COMPONENT_FIRSTNAME = 'firstname',
        COMPONENT_SURNAME = 'surname',
        COMPONENT_IC = 'ic',
        COMPONENT_IC_TITLE = 'ic_title',
        COMPONENT_IC_PLACE = 'ic_place',
        COMPONENT_IC_FORM = 'ic_form',
        COMPONENT_CERTIFIED = 'certified',
        COMPONENT_PARENT_ID = 'parent_id',
        COMPONENT_CITY = 'city';

    /**
     * @persistent
     * @var int|null
     */
    public $id;

    /**
     * @persistent
     * @var int|null
     */
    public $role;

    /** @var array */
    protected $user;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @param int $role
     */
    public function handleSetRole($role)
    {
        $this->setRole($role);
        $this->redrawControl('form');
    }

    /**
     * @param Form $form
     * @param array $roles
     */
    protected function appendRoleControls(Form $form, array $roles)
    {
        if ($roles) {
            $values = $this->prepareSelectValues($roles, self::COMPONENT_ROLE);

            if (null !== $this->getUser()) {
                $form->addHidden(self::COMPONENT_ROLE)->setValue($this->role ?: null);
            } else {
                $form->addSelect(self::COMPONENT_ROLE, 'backend.control.user.label.role', $values)
                    ->setPrompt('backend.control.user.prompt.role')
                    ->setAttribute('data-set-role-url', $this->link('setRole!'))
                    ->setRequired(true)
                    ->setDefaultValue($this->role ?: null);
            }
        }
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = array_filter($form->getValues(true));

        if (!$this->getUser()) {
            $this->userService->createUser($values, $this->getPresenter()->getUser());

            $this->flashMessage('backend.control.user.flash.success.create');
        } else {
            $this->userService->editUser($values, $this->getPresenter()->getUser());

            $this->flashMessage('backend.control.user.flash.success.edit');
        }

        $this->getPresenter()->redirect(':Admin:User:');
    }

    /**
     * @param Form $form
     */
    protected function appendControls(Form $form)
    {
        $roles = $this->prepareRoles();

        $this->role = $this->prepareRole($roles);

        $this->appendRoleControls($form, $roles);

        switch (Arrays::get($roles, $this->role, null)) {
            case Authorizator::ROLE_MASTER:
            case Authorizator::ROLE_ADMIN:
                $this->appendAdminRoleControls($form);
            break;

            case Authorizator::ROLE_AGENCY:
                $this->appendAgencyRoleControls($form);
            break;

            case Authorizator::ROLE_MAPPER:
                $this->appendMapperRoleControls($form);
            break;
        }

        $form->addHidden('id', $this->id);
        $form->addSubmit('submit', 'backend.control.user.label.submit');

        $form->setDefaults($this->prepareValues($form));
    }

    /**
     * @param Form $form
     */
    protected function appendAdminRoleControls(Form $form)
    {
        $form->addText(self::COMPONENT_FIRSTNAME, 'backend.control.user.label.firstname', 255)->setRequired(true);
        $form->addText(self::COMPONENT_SURNAME, 'backend.control.user.label.surname', 255)->setRequired(true);

        $form->addHidden(self::COMPONENT_LICENSE, LicenseManager::DEFAULT_ID);

        $this->appendCommonRoleControls($form);
    }

    /**
     * @param Form $form
     */
    protected function appendAgencyRoleControls(Form $form)
    {
        $form->addText(self::COMPONENT_IC, 'backend.control.user.label.ic', 255)->setRequired(true)
            ->setAttribute('data-get-ares-data', $this->link('getAresData!'));
        $form->addText(self::COMPONENT_IC_TITLE, 'backend.control.user.label.icTitle', 255)->setRequired(true);
        $form->addText(self::COMPONENT_IC_PLACE, 'backend.control.user.label.icPlace', 255)->setRequired(true);
        $form->addText(self::COMPONENT_IC_FORM, 'backend.control.user.label.icForm', 255)->setRequired(true);

        $this->appendLicenseControls($form);

        $user = $this->getPresenter()->getUser();

        if ($user->isInRole(Authorizator::ROLE_MASTER) || $user->isInRole(Authorizator::ROLE_ADMIN)) {
            $form->addCheckbox(self::COMPONENT_CERTIFIED, 'backend.control.user.label.agencyCertified');
        }

        $this->appendCommonRoleControls($form);
    }

    /**
     * @param Form $form
     */
    protected function appendMapperRoleControls(Form $form)
    {
        $user = $this->getPresenter()->getUser();
        $isMasterAdmin = $user->isInRole(Authorizator::ROLE_MASTER) || $user->isInRole(Authorizator::ROLE_ADMIN);

        if ($isMasterAdmin) {
            $agencies = $this->userService->getAgencies();
            $agencies = [null => ''] + Forms::toSelect($agencies, 'id', 'login');

            $form->addSelect(self::COMPONENT_PARENT_ID, 'backend.control.user.label.agency', $agencies);
        }

        $form->addText(self::COMPONENT_FIRSTNAME, 'backend.control.user.label.firstname', 255)->setRequired(true);
        $form->addText(self::COMPONENT_SURNAME, 'backend.control.user.label.surname', 255)->setRequired(true);
        $form->addText(self::COMPONENT_CITY, 'backend.control.user.label.city', 255);

        $this->appendLicenseControls($form, true);

        $certified = $isMasterAdmin;

        if ($user->isInRole(Authorizator::ROLE_AGENCY) && $agency = $this->userService->getUser($user->getId())) {
            $certified = (bool) $agency['certified'];
        }

        if ($certified) {
            $form->addCheckbox(self::COMPONENT_CERTIFIED, 'backend.control.user.label.mapperCertified');
        }

        $this->appendCommonRoleControls($form);

        $form[self::COMPONENT_PHONE]->setRequired(false);
    }

    /**
     * @override Pri editaci nelze manipulovat se heslem.
     *
     * @param Form $form
     */
    protected function appendCommonRoleControls(Form $form)
    {
        parent::appendCommonRoleControls($form);

        if ($this->getUser()) {
            unset($form[self::COMPONENT_LOGIN]);
            unset($form[self::COMPONENT_PASSWORD]);
        }
    }

    /**
     * Pripravy hodnoty z DB pro vypis v selectboxu.
     *
     * @param array $values
     * @param string $namespace
     *
     * @return array
     */
    protected function prepareSelectValues(array $values, $namespace)
    {
        $preparedValues = [];

        $namespace = Strings::toCamelCase($namespace);

        foreach ($values as $key => $value) {
            $preparedValues[$key] = "backend.enum.value.{$namespace}." . Strings::firstLower($value);
        }

        return $preparedValues;
    }

    /**
     * @param array $roles
     *
     * @return int|null
     */
    protected function prepareRole(array $roles)
    {
        if ($user = $this->getUser()) {
            $role = $user['role_id'] ?: $this->role;
        } else if (1 == count($roles)) {
            $keys = array_keys($roles);

            $role = reset($keys);
        } else {
            $role = $this->role;
        }

        return $role;
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    private function prepareValues(Form $form)
    {
        $values = [];

        $user = $this->getUser();

        if ($user) {
            /** @var \Nette\ComponentModel\IComponent $component */
            foreach ($form->getComponents() as $component) {
                $name = $component->getName();

                if (isset($user[$name])) {
                    $values[$name] = $user[$name];
                }
            }
        }

        return $values;
    }

    /**
     * @return array|null
     */
    protected function getUser()
    {
        if (null === $this->user) {
            if (null !== $this->id) {
                $this->user = $this->userService->getUser($this->id, true);
            }
        }

        return $this->user;
    }

    /**
     * @param Form $form
     *
     * @return array|null
     */
    protected function checkEmailContraint(Form $form)
    {
        $user = parent::checkEmailContraint($form);

        if ($user['id'] != $this->id) {
            $result = $user;
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @override Pri editaci nelze manipulovat s loginem
     *
     * @param Form $form
     *
     * @return array|null
     */
    protected function checkLoginContraint(Form $form)
    {
        if (!$this->getUser()) {
            $result = parent::checkLoginContraint($form);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Dohleda informace dle IC z WS ARES
     *
     * @param string $ic
     */
    public function handleGetAresData($ic)
    {
        $ares = new Ares();
        $data = $ares->loadData($ic);

        $res = [];

        if (!empty($data['zip']) && !empty($data['street']) && !empty($data['city'])) {
            $res[self::COMPONENT_IC_PLACE] = "{$data['street']}, {$data['zip']} {$data['city']}";
        }

        if (isset($data['person'])) {
            $res[self::COMPONENT_IC_FORM] = $data['person'] ?
                $this->translator->translate('backend.control.user.ares.FO') :
                $this->translator->translate('backend.control.user.ares.PO');
        }

        if (!empty($data['company'])) {
            $res[self::COMPONENT_IC_TITLE] = $data['company'];
        }

        $response = new JsonResponse($res);
        $this->getPresenter()->sendResponse($response);
    }

    /**
     * @return array
     */
    protected function prepareRoles()
    {
        $roles = $this->userService->getRoles();

        if ($this->getPresenter()->getUser()->isInRole(Authorizator::ROLE_AGENCY) && null === $this->getUser()) {
            $key = array_search(Authorizator::ROLE_MAPPER, $roles, true);

            $roles = [$key => Arrays::get($roles, $key)];
        }

        return $roles;
    }
}
