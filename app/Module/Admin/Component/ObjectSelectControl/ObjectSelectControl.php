<?php

namespace MP\Module\Admin\Component\ObjectSelectControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Service\ObjectRestrictorBuilder;
use MP\Module\Admin\Service\ObjectService;
use MP\Module\Admin\Service\ObjectSuggestionProvider;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;

/**
 * Komponenta pro vyber mapoveho objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectSelectControl extends AbstractFormControl
{
    /** @const Nazev komponenty s nazvem objektu */
    const COMPONENT_TITLE = 'term';

    /** @const Nazev komponenty s vybranym objektem */
    const COMPONENT_ID = 'object';

    /**
     * @persistent
     * @var int
     */
    public $id;

    /** @var callable[] */
    public $onObjectSelected = [];

    /** @var ObjectService */
    protected $objectService;

    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /** @var ObjectSuggestionProvider */
    protected $suggestionProvider;

    /**
     * @param FormFactory $factory
     * @param ObjectService $objectService
     * @param ObjectRestrictorBuilder $restrictorBuilder
     * @param ObjectSuggestionProvider $suggestionProvider
     *
     * @internal param Request $request
     */
    public function __construct(
        FormFactory $factory,
        ObjectService $objectService,
        ObjectRestrictorBuilder $restrictorBuilder,
        ObjectSuggestionProvider $suggestionProvider
    ) {
        parent::__construct($factory);

        $this->objectService = $objectService;
        $this->restrictorBuilder = $restrictorBuilder;
        $this->suggestionProvider = $suggestionProvider;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->required = (bool) $this->id;
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
     * @throws \Nette\Application\BadRequestException
     */
    public function handleAutocomplete()
    {
        try {
            $payload = $this->suggestionProvider->provide($this->id);
        } catch (\Nette\InvalidStateException $e) {
            throw new \Nette\Application\BadRequestException($e->getMessage());
        }

        $response = new JsonResponse($payload);

        $this->getPresenter()->sendResponse($response);
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues(true);

        $this->onObjectSelected($values);
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

        $titleControl = $form->addText(self::COMPONENT_TITLE, 'backend.control.objectSelect.control.label.title');
        $titleControl->setRequired(true);

        $idControl = $form->addText(self::COMPONENT_ID, 'backend.control.objectSelect.control.label.title');

        if (null !== $this->id) {
            $idControl->addConditionOn($titleControl, Form::FILLED)->addRule(Form::REQUIRED, 'backend.control.objectSelect.error.objectNotFound');
        }

        $titleControl->getControlPrototype()->class[] = 'nwjs_autocomplete';
        $titleControl->getControlPrototype()->addAttributes([
            'data-source' => $this->link('autocomplete!'),
            'data-target' => $idControl->getHtmlId()
        ]);

        $form->addSubmit('submit', 'backend.control.objectSelect.control.label.submit');

        return $form;
    }
}
