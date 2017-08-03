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
use Nette\Localization\ITranslator;
use Nette\Utils\Html;

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

    /** @var ITranslator */
    protected $translator;

    /** @var array */
    protected $categories;

    /**
     * @param FormFactory $formFactory
     * @param ObjectRestrictorBuilder $restrictorBuilder
     * @param FilterService $filterService
     * @param ITranslator $translator
     * @param array $categories
     */
    public function __construct(
        FormFactory $formFactory,
        ObjectRestrictorBuilder $restrictorBuilder,
        FilterService $filterService,
        ITranslator $translator,
        array $categories
    ) {
        parent::__construct($formFactory);

        $this->restrictorBuilder = $restrictorBuilder;
        $this->filterService = $filterService;
        $this->categories = $categories;
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
        $groups = $this->prepareCategoryGroups();

        if ($groups) {
            $values = [];

            foreach ($groups as $group => $count) {
                $value = $this->translator->translate("messages.control.filter.values." . ObjectRestrictorBuilder::RESTRICTION_CATEGORY . "." . Strings::firstLower($group));

                $values[$group] = Html::el()->setHtml("{$value}<span>{$count}</span>");
            }

            $defaults = [];

            if ($activeCategories = $this->restrictorBuilder->getCategories()) {
                foreach ($activeCategories as $activeCategory) {
                    foreach ($this->categories as $key => $ids) {
                        if (isset($values[$key]) && in_array($activeCategory, $ids)) {
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
     * Pripravi skupiny typu objektu. Vystupem jsou pouze ty typy, ktere maji nejake objekty serazene sestupne. Typ jine
     * je manualne razen na konec.
     *
     * @return array
     */
    protected function prepareCategoryGroups()
    {
        $counts = $this->filterService->getCategoryCounts();

        $groups = [];

        foreach ($this->categories as $group => $ids) {
            $count = 0;

            foreach ($ids as $id) {
                $count += Arrays::get($counts, $id, 0);
            }

            if ($count > 0) {
                $groups[$group] = $count;
            }
        }

        $other = Arrays::get($groups, 'other', null);

        unset($groups['other']);

        arsort($groups);

        if ($other) {
            $groups['other'] = $other;
        }

        return $groups;
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
            $preparedValues[$key] = Html::el()->setText($this->translator->translate($namespace . Strings::firstLower($value)));
        }

        return $preparedValues;
    }
}
