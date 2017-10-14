<?php

namespace MP\Module\Admin\Component\ObjectOwnerControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Service\ObjectService;
use MP\Module\Admin\Service\UserService;
use MP\Util\Forms;
use Nette\Application\UI\Form;

/**
 * Komponenta pro vykresleni volby majitele objektu.
 */
class ObjectOwnerControl extends AbstractFormControl
{
    /** @const Nazev form prvku s IDckem uzivatele */
    const COMPONENT_ID = 'owner';

    /**
     * @persistent
     * @var int
     */
    public $id;

    /** @var callable[] */
    public $onOwnerSelected = [];

    /** @var UserService */
    protected $userService;

    /** @var ObjectService */
    protected $objectService;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     * @param ObjectService $objectService
     *
     * @internal param Request $request
     */
    public function __construct(FormFactory $factory, UserService $userService, ObjectService $objectService)
    {
        parent::__construct($factory);

        $this->userService = $userService;
        $this->objectService = $objectService;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->currentOwnerName = $this->getCurrenctOwnerName($this->id);
        $template->render();
    }

    /**
     * @param boolean $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues(true);

        $this->onOwnerSelected($values);
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

        $values = $this->userService->getUsers($this->getPresenter()->getUser(), [], false);

        if (1 < count($values)) {
            $values = [null => ''] + Forms::toSelect($values, 'id', 'login');

            $form->addSelect(self::COMPONENT_ID, 'backend.control.ownerSelect.label.owner', $values);
        }

        $form->addSubmit('submit', 'backend.control.ownerSelect.label.submit');

        return $form;
    }

    /**
     * @param int $id
     * @return string
     */
    protected function getCurrenctOwnerName($id)
    {
        $ret = '';
        $object = $this->objectService->getObjectValuesByObjectId($id);

        if ($object) {
            $userId = $object['user_id'];
            $user = $this->userService->getUser($userId);
            $ret = $user['fullname'];
        }

        return $ret;
    }
}
