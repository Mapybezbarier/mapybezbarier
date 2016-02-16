<?php

namespace MP\Module\Admin\Component\AbstractObjectControl\Service;

use MP\Component\Form\Control\BooleanList;
use MP\Component\Form\Control\DateInput;
use MP\Component\Form\Control\RadioList;
use MP\Exchange\Service\ExchangeMetadata;
use MP\Manager\ManagerFactory;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Validators;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FormGenerator
{
    /** @var ExchangeMetadata */
    protected $metadata;

    /** @var ManagerFactory */
    protected $managerFactory;

    /** @var Cache */
    protected $cache;

    /**
     * @param ExchangeMetadata $metadata
     * @param ManagerFactory $managerFactory
     * @param Cache $cache
     */
    public function __construct(ExchangeMetadata $metadata, ManagerFactory $managerFactory, Cache $cache) {
        $this->metadata = $metadata;
        $this->managerFactory = $managerFactory;
        $this->cache = $cache;
    }

    /**
     * @param array $properties
     * @param array $hiddenProperties
     *
     * @return array
     */
    public function generateObjectControls(array $properties, array $hiddenProperties)
    {
        return $this->generateControls(
            ObjectMetadata::OBJECT, array_combine($properties, $properties), null, $hiddenProperties
        );
    }

    /**
     * @param int $index
     *
     * @return array
     */
    public function generateEntraceControls($index)
    {
        $properties = [];

        $prefix = "entrance{$index}";

        foreach ($this->metadata->getProperties() as $property) {
            if (Validators::is($property, 'scalar') && Strings::startsWith($property, $prefix)) {
                $key = Strings::substring($property, Strings::length($prefix));
                $key = Strings::firstLower($key);

                $properties[$key] = $property;
            }
        }

        $controls = $this->generateControls('entrance', $properties);

        return $controls;
    }

    /***
     * @param array $ignored
     *
     * @return array
     */
    public function generateInteriorControls(array $ignored = [])
    {
        $ignored = array_merge($ignored, ['objectId']);

        $properties = $this->metadata->getProperties();

        $properties = array_filter($properties, function($property) use ($ignored) {
            return Validators::is($property, 'scalar') && Strings::startsWith($property, "object") && !in_array($property, $ignored, true);
        });

        $controls = $this->generateControls('interior', array_combine($properties, $properties));

        return $controls;
    }

    /**
     * @param IContainer $container
     * @param string $attachement
     *
     * @return array
     */
    public function generateAttachementControls(IContainer $container, $attachement)
    {
        $properties = Arrays::get($this->metadata->getProperties(), $attachement, []);

        $controls = $this->generateControls($container->getName(), array_combine($properties, $properties), $attachement);

        return $controls;
    }

    /**
     * @param string $group
     * @param array $properties
     * @param string|null $namespace
     * @param array $hiddenProperties
     *
     * @return array
     */
    protected function generateControls($group, array $properties, $namespace = null, $hiddenProperties = [])
    {
        $controls = [];

        foreach ($properties as $label => $property) {
            $label = "backend.control.object.label.{$group}." . $label;

            $rule = $this->metadata->getRule($namespace, $property);
            $type = $this->metadata->getType($namespace, $property);

            switch ($type) {
                case ExchangeMetadata::TYPE_STRING:
                    if ($values = Arrays::get($rule, ExchangeMetadata::RULE_ENUM, [])) {
                        $control = new RadioList($label, $this->prepareEnumValues($values, $property, $namespace));
                    } else {
                        $control = new TextInput($label);
                    }
                break;

                case ExchangeMetadata::TYPE_INTEGER:
                    $control = new TextInput($label);
                    $control->addCondition(Form::FILLED)->addRule(Form::INTEGER);
                break;

                case ExchangeMetadata::TYPE_NUMBER:
                    $control = new TextInput($label);
                    $control->addCondition(Form::FILLED)->addRule(Form::FLOAT);
                break;

                case ExchangeMetadata::TYPE_BOOLEAN:
                    $control = new BooleanList($label, [
                        true => "backend.control.object.value.yes",
                        false => "backend.control.object.value.no",
                    ]);
                break;

                case ExchangeMetadata::TYPE_DATE_TIME:
                    $control = new DateInput($label);
                break;

                default:
                    continue;
            }

            if (in_array($property, $hiddenProperties)) {
                $control->getControlPrototype()->class[] = 'hidden';
            }

            $control->setRequired(Arrays::get($rule, ExchangeMetadata::RULE_REQUIRED, false));

            if ($minimum = Arrays::get($rule, ExchangeMetadata::RULE_MINIMUM, false)) {
                $control->addCondition(Form::FILLED)->addRule(Form::MIN, null, $minimum);
            }

            if ($maximum = Arrays::get($rule, ExchangeMetadata::RULE_MAXIMUM, false)) {
                $control->addCondition(Form::FILLED)->addRule(Form::MAX, null, $maximum);
            }

            $controls[$property] = $control;
        }

        return $controls;
    }

    /**
     * @param array $values
     * @param string $property
     * @param string|null $namespace
     *
     * @return array
     */
    protected function prepareEnumValues(array $values, $property, $namespace = null)
    {
        $namespace = $namespace ?: ObjectMetadata::OBJECT;

        $tableName = ObjectMetadata::$ENUM_COLUMN_TABLE_MAPPING[$namespace][Strings::toUnderscore($property)];

        $preparedValues = [];

        foreach ($values as $value) {
            $preparedValues[$value] = "backend.control.object.value." . Strings::toCamelCase($tableName) . "." . Strings::firstLower($value);
        }

        return $preparedValues;
    }
}
