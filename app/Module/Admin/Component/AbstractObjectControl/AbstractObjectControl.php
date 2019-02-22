<?php

namespace MP\Module\Admin\Component\AbstractObjectControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\Control\RadioList;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Component\AbstractObjectControl\Service\FormGenerator;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\ObjectService;
use MP\Object\ObjectMetadata;
use Nette\Application\UI\Form;
use Nette\Application\UI\ITemplate;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Controls\TextInput;
use Nette\Localization\ITranslator;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractObjectControl extends AbstractFormControl
{
    /** @const Nazev komponenty formulare */
    const COMPONENT_FORM = 'form';

    /** @const Nazvy komponent formulare */
    const COMPONENT_OBJECT_TYPE = 'objectType',
        COMPONENT_OBJECT_CUSTOM_TYPE = 'objectTypeCustom',
        COMPONENT_IMAGE = 'image',
        COMPONENT_DESCRIPTION = 'description',
        COMPONENT_ID = 'id',
        COMPONENT_OBJECT_ID = 'objectId';

    /**
     * @persistent
     * @var string
     */
    public $tab = 'object';

    /** @var array */
    protected $attachements;

    /** @var FormGenerator */
    protected $formGenerator;

    /** @var ObjectService */
    protected $objectService;

    /** @var ITranslator */
    protected $translator;

    /**
     * @param FormFactory $factory
     * @param FormGenerator $formGenerator
     * @param ObjectService $objectService
     * @param ITranslator $translator
     */
    public function __construct(FormFactory $factory, FormGenerator $formGenerator, ObjectService $objectService, ITranslator $translator) {
        parent::__construct($factory);

        $this->formGenerator = $formGenerator;
        $this->objectService = $objectService;
        $this->translator = $translator;
    }

    /**
     * @param string $tab
     */
    public function handleSetTab($tab)
    {
        $this->tab = $tab;
    }

    /**
     * @param ITemplate $template
     */
    protected function prepareTemplateVars(ITemplate $template)
    {
        $template->attachements = $this->prepareAttachements();
        $template->translator = $this->translator;
        $template->tab = $this->tab;
    }

    /**
     * @param string $name
     *
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = $this->factory->create($this, $name);

        $this->prepareForm($form);

        $this->appendObjectControls($form);
        $this->appendAttachementsControls($form);

        $this->prepareControls($form->getControls());

        $form->setDefaults($this->prepareDefaults());

        return $form;
    }

    /**
     * @param Form $form
     */
    protected function prepareForm(Form $form)
    {
        $form->getElementPrototype()->addAttributes(['class' => 'nwjs_object_form']);
    }

    /**
     * @param \ArrayIterator $controls
     */
    protected function prepareControls($controls)
    {

    }

    /**
     * @return array
     * @throws \Nette\Utils\JsonException
     */
    protected function prepareDefaults()
    {
        $defaults = [];

        return $defaults;
    }

    /**
     * @param Form $form
     */
    protected function appendObjectControls(Form $form)
    {
        $controls = $this->formGenerator->generateObjectControls(
            $this->getObjectProperties(), $this->getObjectHiddenProperties()
        );

        $this->appendControls($form, $controls);

        // vstupy
        foreach ([1, 2] as $index) {
            $this->appendControls($form, $this->formGenerator->generateEntraceControls($index));
        }

        // interier
        $this->appendControls($form, $this->formGenerator->generateInteriorControls($this->getObjectProperties()));

        $this->appendObjectIdControls($form);
        $this->appendObjectDescriptionControls($form);
        $this->appendObjectCustomTypeControls($form);
    }

    /**
     * @param Form $form
     */
    protected function appendAttachementsControls(Form $form)
    {
        foreach ($this->prepareAttachements() as $attachement => $indexes) {
            $form->addContainer($attachement);

            foreach ($indexes as $index) {
                $this->appendAttachementControls($form, $attachement, $index);
            }
        }
    }

    /**
     * @param Form $form
     * @param string $attachement
     * @param int $index
     */
    protected function appendAttachementControls(Form $form, $attachement, $index)
    {
        $this->appendControls($form[$attachement]->addContainer($index), $this->formGenerator->generateAttachementControls($form[$attachement], $attachement));
    }

    /**
     * @return array
     */
    protected function prepareAttachements()
    {
        if (!isset($this->attachements)) {
            $this->attachements = $this->getDefaultAttachements();
        }

        return $this->attachements;
    }

    /**
     * @param IContainer $container
     * @param array $controls
     */
    protected function appendControls(IContainer $container, array $controls)
    {
        foreach ($controls as $name => $control) {
            $container->addComponent($control, $name);
        }
    }

    /**
     * @return array
     */
    protected function getObjectProperties()
    {
        $properties = [
            'title',
            'parentObjectId',
            'organizationName',
            'mappingDate',
            'ruianAddress',
            'zipcode',
            'region',
            'city',
            'cityPart',
            'street',
            'streetDescNo',
            'streetNoIsAlternative',
            'streetOrientNo',
            'streetOrientSymbol',
            'objectType',
            'objectTypeCustom',
            'accessibility',
            'accessibilityPram',
            'accessibilityPensioners',
            'latitude',
            'longitude',
            'webUrl',
            'dataOwnerUrl',
        ];

        $user = $this->getPresenter()->getUser();

        if ($user->isInRole(Authorizator::ROLE_MASTER) || $user->isInRole(Authorizator::ROLE_ADMIN)) {
            $properties[] = 'certified';
        }

        return $properties;
    }

    /**
     * @return array
     */
    protected function getObjectHiddenProperties()
    {
        return [];
    }

    /**
     * Vrati vychozi prilohy
     *
     * @return array
     */
    protected function getDefaultAttachements()
    {
        $attachements = [
            ObjectMetadata::RAMP_SKIDS => [],
            ObjectMetadata::PLATFORM => [],
            ObjectMetadata::ELEVATOR => [],
            ObjectMetadata::WC => [],
        ];

        return $attachements;
    }

    /**
     * @param Form $form
     */
    protected function appendObjectIdControls(Form $form)
    {
        $form->addHidden(self::COMPONENT_ID);
        $form->addHidden(self::COMPONENT_OBJECT_ID);
    }

    /**
     * @param Form $form
     */
    protected function appendObjectDescriptionControls(Form $form)
    {
        $form->addTextArea(self::COMPONENT_DESCRIPTION, 'backend.control.object.label.object.description');
    }

    /**
     * @param Form $form
     */
    protected function appendObjectCustomTypeControls(Form $form)
    {
        /** @var RadioList $objectType */
        $objectType = $form[self::COMPONENT_OBJECT_TYPE];

        $types = $objectType->getItems();

        $locale = setlocale(LC_ALL, 0);

        setlocale(LC_ALL, 'cs_CZ.UTF8');

        uasort($types, function ($a, $b) {
            $a = $this->translator->translate($a);
            $b = $this->translator->translate($b);

            return strcoll($a, $b);
        });

        setlocale(LC_ALL, $locale);

        unset($types[ObjectMetadata::CATEGORY_OTHER]);
        $types += [ObjectMetadata::CATEGORY_OTHER => 'backend.control.object.value.objectType.otherObjectCategory'];

        $objectType->setItems($types);

        /** @var TextInput $customObjectType */
        $customObjectType = $form[self::COMPONENT_OBJECT_CUSTOM_TYPE];
        $customObjectType->setAttribute('data-id', ObjectMetadata::CATEGORY_OTHER);
        $customObjectType->addConditionOn($form[self::COMPONENT_OBJECT_TYPE], Form::EQUAL, ObjectMetadata::CATEGORY_OTHER)->addRule(Form::REQUIRED);
    }
}
