<?php

namespace MP\Module\Admin\Component;

use MP\Component\Form\AbstractFormControl;
use MP\Util\Arrays;
use Nette\Application\UI\Form;

/**
 * Predek komponent pro vyhledani zaznamu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractSearchControl extends AbstractFormControl
{
    /** @const Nazvy komponent */
    const COMPONENT_SUBMIT = 'submit',
        COMPONENT_RESET = 'reset';

    /** @var callable[] */
    public $onFilterChange = [];

    /**
     * Nastaveni poli formulare.
     *
     * @param Form $form
     */
    abstract protected function appendControls(Form $form);

    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = [];

        if ($form[self::COMPONENT_SUBMIT]->isSubmittedBy()) {
            $values = $form->getValues(true);
        } else if ($form[self::COMPONENT_RESET]->isSubmittedBy()) {
            $values = [];
        }

        foreach ($this->getParameters() as $key => $value) {
            $this->{$key} = Arrays::get($values, $key, is_array($value) ? [] : null);
        }

        $this->onFilterChange($values);

        $this->getPresenter()->redirect('this');
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

        $this->appendControls($form);

        $values = $this->prepareValues($form);

        $form->setDefaults($values);

        $form->addSubmit(self::COMPONENT_SUBMIT, 'backend.control.search.label.search');
        $form->addSubmit(self::COMPONENT_RESET, 'backend.control.search.label.reset')->setValidationScope(false);

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    protected function prepareValues(Form $form)
    {
        $values = [];

        foreach ($form->getComponents() as $component) {
            $key = $component->getName();
            $value = $this->getParameter($key);

            if (null !== $value) {
                $values[$key] = $value;
            }
        }

        return $values;
    }
}
