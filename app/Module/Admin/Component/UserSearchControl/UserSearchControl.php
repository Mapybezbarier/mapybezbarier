<?php

namespace MP\Module\Admin\Component\UserSearchControl;

use MP\Component\Form\FormFactory;
use MP\Module\Admin\Component\AbstractSearchControl;
use MP\Module\Admin\Service\UserRestrictorBuilder;
use MP\Module\Admin\Service\UserService;
use MP\Util\Strings;
use Nette\Application\UI\Form;

/**
 * Komponenta pro vyhledani ve vypise uzivatelu.
 */
class UserSearchControl extends AbstractSearchControl
{
    /**
     * @persistent
     * @var string
     */
    public $title;

    /**
     * @persistent
     * @var int
     */
    public $roleId;

    /** @var UserService */
    protected $userService;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     */
    public function __construct(FormFactory $factory, UserService $userService)
    {
        parent::__construct($factory);

        $this->userService = $userService;
    }

    /**
     * Nastaveni poli formulare.
     *
     * @param Form $form
     */
    protected function appendControls(Form $form)
    {
        $roles_data = $this->userService->getRoles();
        $roles = [];

        foreach ($roles_data as $key => $value) {
            $roles[$key] = "backend.enum.value." . UserRestrictorBuilder::RESTRICTION_ROLE . "." . Strings::firstLower($value);
        }

        $form->addText(UserRestrictorBuilder::RESTRICTION_TITLE, 'backend.control.userSearch.label.title');
        $form->addSelect(UserRestrictorBuilder::RESTRICTION_ROLE, 'backend.control.userSearch.label.roleId', $roles)
            ->setPrompt('backend.control.userSearch.prompt.roleId');
    }
}
