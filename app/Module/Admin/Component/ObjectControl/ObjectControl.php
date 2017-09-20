<?php

namespace MP\Module\Admin\Component\ObjectControl;

use MP\Component\Form\FormFactory;
use MP\Exchange\Service\ImportLogger;
use MP\Exchange\Service\RuianFinder;
use MP\Exchange\Validator\ValidatorsFactory;
use MP\Manager\ExchangeSourceManager;
use MP\Mapper\IMapper;
use MP\Module\Admin\Component\AbstractObjectControl\AbstractObjectControl;
use MP\Module\Admin\Component\AbstractObjectControl\Service\FormGenerator;
use MP\Module\Admin\Component\ObjectAddressMapControl\IObjectAddressMapControlFactory;
use MP\Module\Admin\Component\ObjectAddressMapControl\ObjectAddressMapControl;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\ObjectDraftService;
use MP\Module\Admin\Service\ObjectRestrictorBuilder;
use MP\Module\Admin\Service\ObjectService;
use MP\Module\Admin\Service\ObjectSuggestionProvider;
use MP\Module\Admin\Service\UserService;
use MP\Object\ObjectHelper;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;
use Nette\Application\UI\ITemplate;
use Nette\Forms\Controls\SubmitButton;
use Nette\Http\Request;
use Nette\Localization\ITranslator;
use Nette\Utils\Json;

/**
 * Komponenta pro zadani udaju mapoveho objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectControl extends AbstractObjectControl
{
    /** @const Nazev parametru s hodnotou pro query naseptavani */
    const PARAM_TERM = 'term';

    /** @const Nazev parametru s hodnotou pro query naseptavani ulice - PSC, obec, cast obce */
    const PARAM_ZIPCODE = 'zipcode';
    const PARAM_CITY = 'city';
    const PARAM_CITY_PART = 'cityPart';
    const PARAM_STREET = 'street';

    /** @const Nazvy komponent */
    const COMPONENT_SAVE = 'save',
        COMPONENT_PUBLISH = 'publish',
        COMPONENT_PUBLISH_WITH_NOTICES = 'publishWithNotices',
        COMPONENT_REMOVE_IMAGE = 'removeImage',
        COMPONENT_REGION = 'region',
        COMPONENT_PARENT_OBJECT = 'parentObject',
        COMPONENT_PARENT_OBJECT_ID = 'parentObjectId',
        COPONENT_ZIPCODE = 'zipcode',
        COMPONENT_STREET = 'street',
        COMPONENT_STREET_DESC_NO = 'streetDescNo',
        COMPONENT_HELP_ADDRESS_1 = 'helpAddress1',
        COMPONENT_HELP_ADDRESS_2 = 'helpAddress2',
        COMPONENT_HELP_ADDRESS_3 = 'helpAddress3',
        COMPONENT_CERTIFIED = 'certified';

    /**
     * @persistent
     * @var int
     */
    public $draft;

    /**
     * @persistent
     * @var bool
     */
    public $mapping = false;

    /** @var UserService */
    protected $userService;

    /** @var ObjectDraftService */
    protected $draftService;

    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /** @var ObjectSuggestionProvider */
    protected $suggestionProvider;

    /** @var IObjectAddressMapControlFactory */
    protected $objectAddressMapFactory;

    /** @var RuianFinder */
    protected $ruianFinder;

    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var ValidatorsFactory */
    protected $validatorsFactory;

    /** @var Request */
    protected $request;

    /**
     * @param FormFactory $factory
     * @param FormGenerator $formGenerator
     * @param ObjectService $objectService
     * @param ITranslator $translator
     * @param UserService $userService
     * @param ObjectDraftService $draftService
     * @param ObjectRestrictorBuilder $restrictorBuilder
     * @param ObjectSuggestionProvider $suggestionProvider
     * @param IObjectAddressMapControlFactory $objectAddressMapFactory
     * @param RuianFinder $ruianFinder
     * @param ExchangeSourceManager $sourceManager
     * @param ValidatorsFactory $validatorsFactory
     * @param Request $request
     */
    public function __construct(
        FormFactory $factory,
        FormGenerator $formGenerator,
        ObjectService $objectService,
        ITranslator $translator,
        UserService $userService,
        ObjectDraftService $draftService,
        ObjectRestrictorBuilder $restrictorBuilder,
        ObjectSuggestionProvider $suggestionProvider,
        IObjectAddressMapControlFactory $objectAddressMapFactory,
        RuianFinder $ruianFinder,
        ExchangeSourceManager $sourceManager,
        ValidatorsFactory $validatorsFactory,
        Request $request
    ) {
        parent::__construct($factory, $formGenerator, $objectService, $translator);

        $this->draftService = $draftService;
        $this->restrictorBuilder = $restrictorBuilder;
        $this->objectAddressMapFactory = $objectAddressMapFactory;
        $this->ruianFinder = $ruianFinder;
        $this->validatorsFactory = $validatorsFactory;
        $this->sourceManager = $sourceManager;
        $this->suggestionProvider = $suggestionProvider;
        $this->request = $request;
        $this->userService = $userService;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $this->prepareTemplateVars($template);
        $template->render();
    }

    /**
     * @param array $object
     */
    public function setObject(array $object)
    {
        $draft = $this->draftService->createByObject($object);

        $this->draft = $draft[IMapper::ID];
    }

    /**
     * @param array $draft
     */
    public function setDraft(array $draft)
    {
        $this->draft = $draft[IMapper::ID];
    }

    /**
     * @param boolean $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        if ($form[self::COMPONENT_SAVE]->isSubmittedBy() || $form[self::COMPONENT_REMOVE_IMAGE]->isSubmittedBy()) {
            $this->saveObject($form);
        } else if ($form[self::COMPONENT_PUBLISH]->isSubmittedBy() || $form[self::COMPONENT_PUBLISH_WITH_NOTICES]->isSubmittedBy()) {
            $this->publishObject($form);
        }
    }

    /**
     * Handler autosave
     */
    public function handleAutosave()
    {
        $values = $this->request->getPost();

        $this->draftService->saveDraft($this->draft, $values, $this->prepareAttachements());

        $response = new JsonResponse(['status' => 'ok']);

        $this->getPresenter()->sendResponse($response);
    }

    /**
     * Dohledani PSC, obce a casti obce z 1 vstupu
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleAutocompleteAddress1()
    {
        $term = $this->request->getQuery(self::PARAM_TERM, null);

        if ($term) {
            $term = Strings::replace($term, '~\s+~', '');

            $items = $this->ruianFinder->findZipcodeCityCitypart($term);

            $payload = $groups = [];

            foreach ($items as $item) {
                $title = (
                    $item['city'] !== $item['city_part'] ?
                    "{$item['zipcode']} {$item['city']}, {$item['city_part']}" :
                    "{$item['zipcode']} {$item['city']}"
                );

                $payload[] = [
                    'zipcode' => $item['zipcode'],
                    'city' => $item['city'],
                    'city_part' => $item['city_part'],
                    'label' => $title,
                ];

                $groups[$item['zipcode']][] = [
                    'city' => $item['city'],
                    'city_part' => $item['city_part'],
                ];
            }

            foreach ($groups as $zipcode => $addresses) {
                $count = count($addresses);

                if ($count > 1) {
                    array_unshift($payload, [
                        'zipcode' => $zipcode,
                        'city' => array_unique(
                            array_reduce($addresses, function($carry, $address) {
                                $carry[] = $address['city'];

                                return $carry;
                            }, [])
                        ),
                        'city_part' => array_unique(
                            array_reduce($addresses, function($carry, $address) {
                                $carry[] = $address['city_part'];

                                return $carry;
                            }, [])
                        ),
                        'label' => $this->translator->translate('backend.control.object.label.object.zipcodeAmbiguous', [
                            'zipcode' =>$zipcode,
                            'count' => $count,
                        ]),
                    ]);
                }
            }

            $response = new JsonResponse($payload);

            $this->getPresenter()->sendResponse($response);
        } else {
            throw new \Nette\Application\BadRequestException("Missing or empty term parameter");
        }
    }

    /**
     * Overeni dostupnosti ulice dle vystupu Address1
     * @throws \Nette\Application\BadRequestException
     */
    public function handleCheckAddress2()
    {
        $zipcode = $this->request->getQuery(self::PARAM_ZIPCODE);
        $city = $this->request->getQuery(self::PARAM_CITY);
        $cityPart = $this->request->getQuery(self::PARAM_CITY_PART);

        if ($zipcode && $city && $cityPart) {
            $hasStreet = $this->ruianFinder->hasStreet($zipcode, $city, $cityPart);
            
            $payload = [
                'hasStreet' => $hasStreet,
                'message' => $hasStreet ? null : $this->translator->translate('backend.control.object.text.noStreet'),
            ];

            $response = new JsonResponse($payload);

            $this->getPresenter()->sendResponse($response);
        } else {
            throw new \Nette\Application\BadRequestException('Missing parameters');
        }
    }

    /**
     * Dohledani ulice dle vystupu Address1
     * @throws \Nette\Application\BadRequestException
     */
    public function handleAutocompleteAddress2()
    {
        $term = $this->request->getQuery(self::PARAM_TERM);
        $zipcode = $this->request->getQuery(self::PARAM_ZIPCODE);
        $city = $this->request->getQuery(self::PARAM_CITY);
        $cityPart = $this->request->getQuery(self::PARAM_CITY_PART);

        if ($term && $zipcode && $city && $cityPart) {
            $items = $this->ruianFinder->findStreet($term, $zipcode, $city, $cityPart);

            $payload = [];

            foreach ($items as $item) {
                $title = $item['street'];
                $zipcode = $item['zipcode'];

                if (is_array($city)) {
                    $title .= ", {$item['city']}";
                    $zipcode .= " {$item['city']}";
                }

                if (is_array($cityPart)) {
                    $title .= " - {$item['city_part']}";
                    $zipcode .= ", {$item['city_part']}";
                }

                $payload[] = [
                    'zipcode' => $zipcode,
                    'city' => $item['city'],
                    'city_part' => $item['city_part'],
                    'street' => $item['street'],
                    'label' => $title,
                ];
            }

            $response = new JsonResponse($payload);

            $this->getPresenter()->sendResponse($response);
        } else {
            throw new \Nette\Application\BadRequestException('Missing parameters');
        }
    }

    /**
     * Dohledani cisla domu dle vystupu Address2
     * @throws \Nette\Application\BadRequestException
     */
    public function handleAutocompleteAddress3()
    {
        $term = $this->request->getQuery(self::PARAM_TERM);
        $zipcode = $this->request->getQuery(self::PARAM_ZIPCODE);
        $city = $this->request->getQuery(self::PARAM_CITY);
        $cityPart = $this->request->getQuery(self::PARAM_CITY_PART);
        $street = $this->request->getQuery(self::PARAM_STREET);

        // $street nekontroluji, nektere obce nemaji ulice
        if ($term && $zipcode && $city && $cityPart) {
            $items = $this->ruianFinder->findStreetNumber($term, $zipcode, $city, $cityPart, $street);

            $payload = [];

            foreach ($items as $item) {
                $title = $item['street_orient_no'] ?
                    "{$item['street_desc_no']}/{$item['street_orient_no']}{$item['street_orient_symbol']}" :
                    $item['street_desc_no'];

                $payload[] = [
                    'id' => $item['id'],
                    'street_desc_no' => $item['street_desc_no'],
                    'street_orient_no' => $item['street_orient_no'],
                    'street_orient_symbol' => $item['street_orient_symbol'],
                    'label' => $title
                ];
            }

            $response = new JsonResponse($payload);

            $this->getPresenter()->sendResponse($response);
        } else {
            throw new \Nette\Application\BadRequestException("Missing or empty term parameter");
        }
    }

    /**
     * Dohledani nazvu objektu
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleAutocompleteObject()
    {
        try {
            $payload = $this->suggestionProvider->provide();
        } catch (\Nette\InvalidStateException $e) {
            throw new \Nette\Application\BadRequestException($e->getMessage());
        }

        $response = new JsonResponse($payload);

        $this->getPresenter()->sendResponse($response);
    }

    /**
     * @param Form $form
     */
    protected function saveObject(Form $form)
    {
        $values = $form->getValues(true);

        if ($form[self::COMPONENT_REMOVE_IMAGE]->isSubmittedBy()) {
            unset($values[self::COMPONENT_IMAGE]);
        }

        $draft = $this->draftService->saveDraft($this->draft, $values, $this->prepareAttachements());

        $this->flashMessage('backend.object.flash.save.success');

        $this->getPresenter()->redirect(':Admin:Draft:default', ['id' => $draft[IMapper::ID]]);
    }

    /**
     * @param Form $form
     */
    protected function publishObject(Form $form)
    {
        $values = $form->getValues(true);
        
        $object = $this->prepareObject($values);

        $priority = (empty($object['latitude']) || empty($object['longitude']));

        $draft = $this->draftService->getDraft($this->draft, true);

        $this->objectService->saveObject($object, $priority, $draft);

        $this->flashMessage('backend.object.flash.publish.success');

        $this->getPresenter()->redirect(':Admin:Object:default');
    }

    /**
     * @param array $values
     *
     * @return array
     */
    protected function prepareObject(array $values)
    {
        $fields = [
            self::COMPONENT_HELP_ADDRESS_1,
            self::COMPONENT_HELP_ADDRESS_2,
            self::COMPONENT_HELP_ADDRESS_3,
            self::COMPONENT_PARENT_OBJECT,
        ];

        foreach ($fields as $field) {
            unset($values[$field]);
        }

        Arrays::filter($values, function ($value) {
            return (null !== $value && '' !== $value);
        });

        $user = $this->getPresenter()->getUser();

        // role agentura/mapar nemohou nastavit certifikovanost z formulare, prebira se z jejich uctu
        if ($user->isInRole(Authorizator::ROLE_AGENCY) || $user->isInRole(Authorizator::ROLE_MAPPER)) {
            $values['certified'] = $this->userService->isCertified($user->getId());
        }

        return $values;
    }

    /**
     * @param Form $form
     */
    protected function appendObjectControls(Form $form)
    {
        parent::appendObjectControls($form);

        $this->appendObjectAddressControls($form);
        $this->appendObjectParentObjectControls($form);
        $this->appendObjectImageControls($form);

        $user = $this->getPresenter()->getUser();

        if ($user->isInRole(Authorizator::ROLE_MASTER) || $user->isInRole(Authorizator::ROLE_ADMIN)) {
            $form[self::COMPONENT_CERTIFIED]->setRequired(true);
        }
    }

    /**
     * @param Form $form
     */
    protected function appendAttachementsControls(Form $form)
    {
        parent::appendAttachementsControls($form);

        foreach ([ObjectMetadata::RAMP_SKIDS, ObjectMetadata::PLATFORM, ObjectMetadata::ELEVATOR, ObjectMetadata::WC] as $attachement) {
            $form->addSubmit("add" . Strings::firstUpper($attachement), "backend.control.object.label.attachement.{$attachement}")
                ->setValidationScope(false)
                ->onClick[] = function(SubmitButton $control) use ($attachement) {
                    $this->addAttachement($control, $attachement);
                };
        }
    }

    /**
     * @param Form $form
     * @param string $attachement
     * @param int $index
     */
    protected function appendAttachementControls(Form $form, $attachement, $index)
    {
        parent::appendAttachementControls($form, $attachement, $index);

        $form->addSubmit("remove" . Strings::firstUpper($attachement) . $index, 'backend.control.object.label.removeAttachement')
            ->setValidationScope(false)
            ->onClick[] = function(SubmitButton $control) use ($attachement, $index) {
                $this->removeAttachement($control, $attachement, $index);
            };
    }

    /**
     * @param SubmitButton $control
     * @param string $attachement
     */
    protected function addAttachement(SubmitButton $control, $attachement)
    {
        $values = $control->getForm()->getValues(true);

        $attachements = $this->prepareAttachements();

        $index = $attachements[$attachement] ? end($attachements[$attachement]) + 1 : 0;

        $attachements[$attachement][] = $index;

        $this->tab = "{$attachement}{$index}";

        $this->saveDraft($values, $attachements);
    }

    /**
     * @param SubmitButton $control
     * @param string $attachement
     * @param int $index
     */
    protected function removeAttachement(SubmitButton $control, $attachement, $index)
    {
        $values = $control->getForm()->getValues(true);

        $attachements = $this->prepareAttachements();

        $indexKey = array_search($index, $attachements[$attachement], true);

        unset($attachements[$attachement][$indexKey]);

        $this->tab = 'object';

        $this->saveDraft($values, $attachements);
    }

    /**
     * @param array $values
     * @param array $attachements
     */
    protected function saveDraft(array $values, array $attachements)
    {
        $this->draftService->saveDraft($this->draft, $values, $attachements);

        $this->redirect('this');
    }

    /**
     * Volba adresy:
     *  region: naseptavac znamych regionu
     *  ruianAddress, longitude, latitude: skryte
     *  cela adresa: skryte s pomocmymi inputy a naseptavanim
     *
     * @param Form $form
     *
     * @throws \Nette\Utils\JsonException
     */
    protected function appendObjectAddressControls(Form $form)
    {
        $regions = $this->objectService->getRegions();
        $regions = Json::encode($regions);

        foreach ($this->getReadOnlyProperties() as $property) {
            $form[$property]->setAttribute('readonly', true);
        }

        $form[self::COMPONENT_REGION]->getControlPrototype()->addAttributes(['data-autocomplete' => $regions]);

        // PSC, obec, cast obce
        $helpAddress1 = $form->addText(self::COMPONENT_HELP_ADDRESS_1, 'backend.control.object.label.object.helpAddress1');
        $helpAddress1->getControlPrototype()->addAttributes(['data-source' => $this->link('autocompleteAddress1!')]);
        $form[self::COPONENT_ZIPCODE]->addConditionOn($helpAddress1, Form::FILLED, true)->addRule(Form::REQUIRED, 'backend.control.object.error.address1NotFound');

        // ulice
        $helpAddress2 = $form->addText(self::COMPONENT_HELP_ADDRESS_2, 'backend.control.object.label.object.helpAddress2');
        $helpAddress2->getControlPrototype()->addAttributes([
            'data-check' => $this->link('checkAddress2!'),
            'data-source' => $this->link('autocompleteAddress2!'),
        ]);
        $form[self::COMPONENT_STREET]->addConditionOn($helpAddress2, Form::FILLED, true)->addRule(Form::REQUIRED, 'backend.control.object.error.address2NotFound');

        // cislo domu
        $helpAddress3 = $form->addText(self::COMPONENT_HELP_ADDRESS_3, 'backend.control.object.label.object.helpAddress3');
        $helpAddress3->getControlPrototype()->addAttributes(['data-source' => $this->link('autocompleteAddress3!')]);
        $form[self::COMPONENT_STREET_DESC_NO]->addConditionOn($helpAddress3, Form::FILLED, true)->addRule(Form::REQUIRED, 'backend.control.object.error.address3NotFound');
    }

    /**
     * Vrati property pouze pro cteni
     *
     * @return array
     */
    protected function getReadOnlyProperties()
    {
        return [
            'zipcode',
            'city',
            'cityPart',
            'street',
            'streetDescNo',
            'streetOrientNo',
            'streetOrientSymbol',
            'latitude',
            'longitude'
        ];
    }

    /**
     * @param Form $form
     */
    protected function appendObjectParentObjectControls(Form $form)
    {
        $parentObject = $form->addText(self::COMPONENT_PARENT_OBJECT, 'backend.control.object.label.object.parentObjectId');
        $parentObject->getControlPrototype()->class[] = 'nwjs_autocomplete';

        $parentObjectId = $form[self::COMPONENT_PARENT_OBJECT_ID];
        $parentObjectId->addConditionOn($parentObject, Form::FILLED, true)->addRule(Form::REQUIRED, 'backend.control.object.error.objectNotFound');

        $parentObject->getControlPrototype()->addAttributes([
            'data-source' => $this->link('autocompleteObject!'),
            'data-target' => $parentObjectId->getHtmlId(),
        ]);
    }

    /**
     * @param Form $form
     */
    protected function appendObjectImageControls(Form $form)
    {
        $form->addUpload(self::COMPONENT_IMAGE, 'backend.control.object.label.object.image')
            ->addCondition(Form::FILLED)->addRule(Form::IMAGE);
    }

    /**
     * @param string $name
     *
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = parent::createComponentForm($name);

        $form->addSubmit(self::COMPONENT_REMOVE_IMAGE, 'backend.control.object.label.removeImage')->setValidationScope(false);
        $form->addSubmit(self::COMPONENT_SAVE, 'backend.control.object.label.draft')->setValidationScope(false);
        $form->addSubmit(self::COMPONENT_PUBLISH, 'backend.control.object.label.publish');
        $form->addSubmit(self::COMPONENT_PUBLISH_WITH_NOTICES, 'backend.control.object.label.publishWithNotices');

        return $form;
    }

    /**
     * @param Form $form
     */
    protected function prepareForm(Form $form)
    {
        parent::prepareForm($form);

        $form->getElementPrototype()->addAttributes(['data-autosave' => $this->link('autosave!')]);

        $form->onSuccess[] = [$this, 'processForm'];
        $form->onValidate[] = [$this, 'validateForm'];
    }

    /**
     * Callback validace formulare
     *
     * @param Form $form
     */
    public function validateForm(Form $form)
    {
        if ($form[self::COMPONENT_PUBLISH]->isSubmittedBy() || $form[self::COMPONENT_PUBLISH_WITH_NOTICES]->isSubmittedBy()) {
            $object = $this->prepareObject($form->getValues(true));

            unset($object[self::COMPONENT_ID]);
            unset($object[self::COMPONENT_IMAGE]);

            $source = $this->sourceManager->findOneById(ExchangeSourceManager::DEFAULT_ID);

            $validators = $this->validatorsFactory->create($source);

            foreach ($validators as $validator) {
                try {
                    $validator->validate($object);
                } catch (\MP\Exchange\Exception\ValidationException $e) {
                    ImportLogger::addError($object, $e->getMessage());
                }
            }

            // pri publikaci chci uzivatele upozornit na vsechny chyby
            if ($form[self::COMPONENT_PUBLISH]->isSubmittedBy()) {
                if (ImportLogger::hasErrors() || ImportLogger::hasNotices()) {
                    $form->addError($this->translator->translate("backend.control.object.error.validation"));
                }
            }

            // pri publikaci s chybami konzistenci chci uzivatele upozornit pouze na chyby kvality
            if ($form[self::COMPONENT_PUBLISH_WITH_NOTICES]->isSubmittedBy()) {
                if (ImportLogger::hasErrors()) {
                    $form->addError($this->translator->translate("backend.control.object.error.validation"));
                }
            }
        }
    }

    /**
     * @return array
     * @throws \Nette\Utils\JsonException
     */
    protected function prepareDefaults()
    {
        $defaults = parent::prepareDefaults();

        if ($draft = $this->draftService->getDraft($this->draft)) {
            $defaults = $draft['data'] ?: [];
        }

        if ($this->mapping) {
            $mappingDefaults = [
                self::COMPONENT_ID => Arrays::get($defaults, self::COMPONENT_ID, null),
                self::COMPONENT_OBJECT_ID => Arrays::get($defaults, self::COMPONENT_OBJECT_ID, null)
            ];

            foreach ($defaults as $key => $value) {
                if (in_array($key, $this->getObjectProperties(), true)) {
                    $mappingDefaults[$key] = $value;
                }
            }

            $defaults = $mappingDefaults;

            unset($defaults['accessibility']);
            unset($defaults['mappingDate']);
        }

        if (!empty($defaults['parentObjectId'])) {
            $parent = $this->objectService->getObjectByObjectId($defaults['parentObjectId']);

            $defaults['parentObject'] = $parent['title'];
        }

        return $defaults;
    }

    /**
     * @return array
     */
    protected function prepareAttachements()
    {
        if (!isset($this->attachements)) {
            $this->attachements = parent::prepareAttachements();

            if ($this->draft) {
                $draft = $this->draftService->getDraft($this->draft, true);

                $data = Json::decode($draft['data'], Json::FORCE_ARRAY);

                $attachements = ObjectHelper::getAttachements($data);

                foreach ($attachements as $attachement => $indexes) {
                    foreach ($indexes as $index => $values) {
                        $this->attachements[$attachement][] = $index;
                    }
                }
            }
        }

        return $this->attachements;
    }

    /**
     * @return string|null
     */
    protected function prepareImage()
    {
        $image = null;

        // prioritne beru fotku z draftu
        if ($draft = $this->draftService->getDraft($this->draft)) {
            $image = Arrays::get($draft, self::COMPONENT_IMAGE, null) ?: $image;

            // fotka u draftu neni, ale je navazana na existujici objekt -> zkusim dohledat fotku navazaneho objektu
            if (null === $image && $draft['map_object_object_id']) {
                $object = $this->objectService->getObjectByObjectId($draft['map_object_object_id']);

                $image = Arrays::get($object, self::COMPONENT_IMAGE, null) ?: $image;
            }
        }

        return $image;
    }

    /**
     * @return array
     */
    protected function getObjectHiddenProperties()
    {
        $properties = array_merge(parent::getObjectHiddenProperties(), [
            'ruianAddress',
            'zipcode',
            'city',
            'cityPart',
            'street',
            'streetDescNo',
            'streetOrientNo',
            'streetOrientSymbol',
            'latitude',
            'longitude',
        ]);

        return $properties;
    }

    /**
     * @return ObjectAddressMapControl
     */
    protected function createComponentObjectAddressMap()
    {
        $control = $this->objectAddressMapFactory->create();

        return $control;
    }

    /**
     * @override Predani vystupu validace
     *
     * @param ITemplate $template
     */
    protected function prepareTemplateVars(ITemplate $template)
    {
        parent::prepareTemplateVars($template);

        $values = $this->prepareDefaults();

        $template->values = $values;
        $template->image = $this->prepareImage();
        $template->errors = $this->prepareErrors();
        $template->notices = $this->prepareNotices();
        $template->hasAddress = $this->prepareHasAddress($values);
    }

    /**
     * @return array
     */
    protected function prepareErrors()
    {
        $errors = [];

        foreach (ImportLogger::getErrors() as $error) {
            $errors[] = $this->translator->translate("backend.import.error.{$error['message']}", $error['arguments']);
        }

        return $errors;
    }

    /**
     * @return array
     */
    protected function prepareNotices()
    {
        $notices = [];

        foreach (ImportLogger::getNotices() as $notice) {
            $notices[] = $this->translator->translate("backend.import.notice.{$notice['message']}", $notice['arguments']);
        }

        return $notices;
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    protected function prepareHasAddress(array $values)
    {
        $hasAddress = false;

        foreach ($this->getReadOnlyProperties() as $property) {
            if (!empty($values[$property])) {
                $hasAddress = true;
                break;
            }
        }

        return $hasAddress;
    }
}
