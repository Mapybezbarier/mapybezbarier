<?php

namespace MP\Module\Admin\Component\ObjectSearchControl;

use MP\Component\Form\FormFactory;
use MP\Module\Admin\Component\AbstractSearchControl;
use MP\Module\Admin\Service\ObjectRestrictorBuilder;
use MP\Module\Admin\Service\UserService;
use MP\Service\FilterService;
use MP\Util\Forms;
use MP\Util\Strings;
use Nette\Application\UI\Form;

/**
 * Komponenta pro vyhledani objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectSearchControl extends AbstractSearchControl
{
    /**
     * @persistent
     * @var string
     */
    public $term;

    /**
     * @persistent
     * @var int
     */
    public $user;

    /**
     * @persistent
     * @var int[]
     */
    public $accessibility = [];

    /**
     * @persistent
     * @var int[]
     */
    public $category = [];

    /**
     * @persistent
     * @var string[]
     */
    public $type = [];

    /** @var UserService */
    protected $userService;

    /** @var FilterService */
    protected $filterService;

    /**
     * @param FormFactory $factory
     * @param UserService $userService
     * @param FilterService $filterService
     */
    public function __construct(FormFactory $factory, UserService $userService, FilterService $filterService)
    {
        parent::__construct($factory);

        $this->userService = $userService;
        $this->filterService = $filterService;
    }

    /**
     * Nastaveni poli formulare.
     *
     * @param Form $form
     */
    protected function appendControls(Form $form)
    {
        $this->appendAccessibilityControls($form);
        $this->appendTermControls($form);
        $this->appendUserControls($form);
        $this->appendCategoryControls($form);
        $this->appendTypeControls($form);
    }

    /**
     * @param Form $form
     */
    protected function appendTermControls(Form $form)
    {
        $form->addText(ObjectRestrictorBuilder::RESTRICTION_TERM, 'backend.control.objectSearch.label.term');
    }

    /**
     * @param Form $form
     */
    protected function appendUserControls(Form $form)
    {
        $values = $this->userService->getUsers($this->getPresenter()->getUser());

        if (1 < count($values)) {
            $values = [null => ''] + Forms::toSelect($values, 'id', 'login');

            $form->addSelect(ObjectRestrictorBuilder::RESTRICTION_USER, 'backend.control.objectSearch.label.user', $values);
        }
    }

    /**
     * @param Form $form
     */
    protected function appendTypeControls(Form $form)
    {
        $values = $this->prepareSelectValues($this->filterService->getTypeValues(), "messages.enum.value." . ObjectRestrictorBuilder::RESTRICTION_TYPE . ".");

        $form->addMultiSelect(ObjectRestrictorBuilder::RESTRICTION_TYPE, 'backend.control.objectSearch.label.type', $values);
    }

    /**
     * @param Form $form
     */
    protected function appendCategoryControls(Form $form)
    {
        $values = $this->prepareSelectValues($this->filterService->getCategoryValues(), "messages.enum.value." . ObjectRestrictorBuilder::RESTRICTION_CATEGORY . ".");

        $form->addMultiSelect(ObjectRestrictorBuilder::RESTRICTION_CATEGORY, 'backend.control.objectSearch.label.category', $values);
    }

    /**
     * @param Form $form
     */
    protected function appendAccessibilityControls(Form $form)
    {
        $values = $this->prepareSelectValues($this->filterService->getAccesibilityValues(), "messages.enum.value." . ObjectRestrictorBuilder::RESTRICTION_ACCESSIBILITY . ".");

        $form->addMultiSelect(ObjectRestrictorBuilder::RESTRICTION_ACCESSIBILITY, 'backend.control.objectSearch.label.accessibility', $values);
    }

    /**
     * Pripravy hodnoty z DB pro vypis v selectoboxu.
     *
     * @param array $values
     * @param string $namespace
     * @return array
     */
    protected function prepareSelectValues(array $values, $namespace)
    {
        $preparedValues = [];

        foreach ($values as $key => $value) {
            $preparedValues[$key] = $namespace . Strings::firstLower($value);
        }

        return $preparedValues;
    }
}
