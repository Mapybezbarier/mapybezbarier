<?php

namespace MP\Module\Admin\Component\LogSearchControl;

use MP\Component\Form\FormFactory;
use MP\Module\Admin\Component\AbstractSearchControl;
use MP\Module\Admin\Service\LogRestrictorBuilder;
use MP\Module\Admin\Service\UserService;
use MP\Util\Forms;
use Nette\Application\UI\Form;

/**
 * Komponenta pro vyhledani v logu akci uzivatelu.
 */
class LogSearchControl extends AbstractSearchControl
{
    /**
     * @persistent
     * @var string
     */
    public $moduleKey;

    /**
     * @persistent
     * @var string
     */
    public $actionKey;

    /**
     * @persistent
     * @var int
     */
    public $userId;

    /**
     * @persistent
     * @var string
     */
    public $changedId;

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
        $users = $this->userService->getUsers($this->getPresenter()->getUser());
        $users = [null => ''] + Forms::toSelect($users, 'id', 'login');

        $form->addText(LogRestrictorBuilder::RESTRICTION_MODULE, 'backend.control.logSearch.label.moduleKey');
        $form->addText(LogRestrictorBuilder::RESTRICTION_ACTION, 'backend.control.logSearch.label.actionKey');
        $form->addSelect(LogRestrictorBuilder::RESTRICTION_USER, 'backend.control.logSearch.label.userId', $users);
        $form->addText(LogRestrictorBuilder::RESTRICTION_ID, 'backend.control.logSearch.label.changedId');
    }
}
