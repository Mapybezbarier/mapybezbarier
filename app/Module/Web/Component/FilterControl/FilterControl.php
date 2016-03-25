<?php

namespace MP\Module\Web\Component\FilterControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Module\Web\Service\ObjectRestrictorBuilder;
use MP\Module\Web\Presenters\HomepagePresenter;
use MP\Service\FilterService;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Application\UI\Form;

/**
 * Komponenta pro filtrovani objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FilterControl extends AbstractFormControl
{
    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /** @var FilterService */
    protected $filterService;

    /** @var array */
    protected $categories;

    /**
     * @param FormFactory $formFactory
     * @param ObjectRestrictorBuilder $restrictorBuilder
     * @param FilterService $filterService
     * @param array $categories
     */
    public function __construct(FormFactory $formFactory, ObjectRestrictorBuilder $restrictorBuilder, FilterService $filterService, array $categories)
    {
        parent::__construct($formFactory);

        $this->restrictorBuilder = $restrictorBuilder;
        $this->filterService = $filterService;
        $this->categories = $categories;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->maps = isset($_GET[HomepagePresenter::PARAM_MAPS])? TRUE : NULL;
        $template->render();
    }

    /**
     * @param Form $form
     */
    public function setFilter(Form $form)
    {
        $values = $form->getValues(true);

        $categories = [];

        foreach (Arrays::get($values, ObjectRestrictorBuilder::RESTRICTION_CATEGORY, []) as $category) {
            $categories = array_merge($categories, Arrays::get($this->categories, $category, []));
        }

        $values[ObjectRestrictorBuilder::RESTRICTION_CATEGORY] = array_unique($categories);

        $this->restrictorBuilder->prepareRestrictions($values, true);
    }

    /**
     * @param string $name
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = $this->factory->create($this, $name);
        $form->getElementPrototype()->class[] = 'ajax nwjs_auto_submit';
        $form->getElementPrototype()->data('spinner', '#map');
        $form->onSuccess[] = [$this, 'setFilter'];

        $this->appendAccessibility($form);
        $this->appendTypes($form);
        $this->appendCategories($form);

        return $form;
    }

    /**
     * @param Form $form
     */
    protected function appendAccessibility(Form $form)
    {
        $accessibility = $this->filterService->getAccesibilityValues();

        if ($accessibility) {
            $values = $this->prepareSelectValues($accessibility,  "messages.enum.value." . ObjectRestrictorBuilder::RESTRICTION_ACCESSIBILITY . ".");

            $form->addCheckboxList(ObjectRestrictorBuilder::RESTRICTION_ACCESSIBILITY, 'messages.control.filter.label.accessibility', $values)
                ->setDefaultValue($this->restrictorBuilder->getAccesibility());
        }
    }

    /**
     * @param Form $form
     */
    protected function appendCategories(Form $form)
    {
        $categories = array_keys($this->categories);
        $categories = array_combine($categories, $categories);

        if ($categories) {
            $values = $this->prepareSelectValues($categories, "messages.control.filter.values." . ObjectRestrictorBuilder::RESTRICTION_CATEGORY . ".");

            $defaults = [];

            if ($activeCategories = $this->restrictorBuilder->getCategories()) {
                foreach ($activeCategories as $activeCategory) {
                    foreach ($this->categories as $key => $ids) {
                        if (in_array($activeCategory, $ids)) {
                            $defaults[] = $key;
                        }
                    }
                }
            }

            $form->addCheckboxList(ObjectRestrictorBuilder::RESTRICTION_CATEGORY, 'messages.control.filter.label.category', $values)
                ->setDefaultValue($defaults);
        }
    }

    /**
     * @param Form $form
     */
    protected function appendTypes(Form $form)
    {
        $types = $this->filterService->getTypeValues();

        if ($types) {
            $values = $this->prepareSelectValues($types, "messages.enum.value." . ObjectRestrictorBuilder::RESTRICTION_TYPE . ".");

            $form->addCheckboxList(ObjectRestrictorBuilder::RESTRICTION_TYPE, 'messages.control.filter.label.type', $values)
                ->setDefaultValue($this->restrictorBuilder->getTypes());
        }
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
