<?php

namespace MP\Module\Admin\Component\ObjectCompareControl;

use MP\Module\Admin\Component\AbstractObjectControl\AbstractObjectControl;
use MP\Object\ObjectHelper;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Application\UI\ITemplate;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\TextBase;
use Nette\Utils\Validators;

/**
 * Komponenta pro porovnani objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectCompareControl extends AbstractObjectControl
{
    /** @var array */
    protected $version;
    
    public function render()
    {
        $template = $this->getTemplate();
        $this->prepareTemplateVars($template);
        $template->render();
    }

    /**
     * @param array $version
     */
    public function setVersion(array $version)
    {
        $this->version = $version;
    }

    /**
     * @param ITemplate $template
     */
    protected function prepareTemplateVars(ITemplate $template)
    {
        parent::prepareTemplateVars($template);

        $template->current = $this->prepareCurrent();
        $template->hasAddress = true;
    }

    /**
     * @return array
     */
    protected function prepareAttachements()
    {
        if (!isset($this->attachements)) {
            $this->attachements = parent::prepareAttachements();

            if ($this->version) {
                $attachements = ObjectHelper::getAttachements($this->version);

                foreach ($attachements as $attachement => $indexes) {
                    foreach ($indexes as $index => $values) {
                        $this->attachements[$attachement][] = $index;
                    }
                }

                $this->version = array_merge($this->version, $attachements);
            }
        }

        return $this->attachements;
    }

    /**
     * @return array
     */
    protected function prepareCurrent()
    {
        $current = $this->objectService->getObjectValuesByObjectId($this->version['object_id']);
        $current = $this->prepareObject($current);

        $values = $this->prepareCurrentValues($current, $this[self::COMPONENT_FORM]->getComponents());

        return $values;
    }

    /**
     * @param array $current
     * @param \Iterator $components
     *
     * @return array
     */
    protected function prepareCurrentValues(array &$current, $components)
    {
        $values = [];

        foreach ($components as $component) {
            $name = Strings::toUnderscore($component->getName());

            if (isset($current[$name])) {
                if ($component instanceof TextBase) {
                    $value = $current[$name];

                    if ($value instanceof \DateTime) {
                        $value = $current[$name]->format($this->translator->translate('backend.format.date'));
                    }

                    $values[Strings::toCamelCase($name)] = $value;
                } else if ($component instanceof RadioList) {
                    $value = $current[$name];
                    $value = (Validators::is($value, 'bool') ? (int) $value : $value);
                    $value = Arrays::get($component->getItems(), $value, null);

                    $values[Strings::toCamelCase($name)] = $this->translator->translate($value);
                } else if ($component instanceof IContainer) {
                    $values[$name] = $this->prepareCurrentValues($current[$name], $component->getComponents());
                }
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    protected function prepareDefaults()
    {
        $defaults = parent::prepareDefaults();

        $attachements = ObjectHelper::getAttachements($this->version);

        $values = $this->prepareObject($this->version);

        foreach ($values as $key => $value) {
            $defaults[Strings::toCamelCase($key)] = $value;
        }

        foreach ($attachements as $attachement => $indexes) {
            foreach ($indexes as $index => $values) {
                foreach ($values as $key => $value) {
                    $defaults[$attachement][$index][Strings::toCamelCase($key)] = $value;
                }
            }
        }

        return $defaults;
    }

    /**
     * @param array $object
     *
     * @return array
     */
    protected function prepareObject(array &$object)
    {
        if ($object['parent_object_id']) {
            $parent = $this->objectService->getObjectByObjectId($object['parent_object_id']);

            $object['parent_object_id'] = $parent['title'];
        }

        return $object;
    }

    /**
     * @param \ArrayIterator $controls
     */
    protected function prepareControls($controls)
    {
        /** @var BaseControl $control */
        foreach ($controls as $control) {
            $control->setDisabled(true);
        }
    }
}
