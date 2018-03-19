<?php

namespace MP\Module\Admin\Component\ManualImportControl;

use MP\Component\FlashMessageControl;
use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Manager\ExchangeSourceManager;
use MP\Manager\LicenseManager;
use MP\Mapper\IMapper;
use MP\Module\Admin\Service\ManualImportService;
use MP\Util\Forms;
use Nette\Forms\Form;

/**
 * Komponenta pro formular pro zalozeni noveho automatickeho importu.
 */
class ManualImportControl extends AbstractFormControl
{
    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var ManualImportService */
    protected $service;

    /**
     * @var LicenseManager
     */
    protected $licenseManager;

    /**
     * @param FormFactory $factory
     * @param ExchangeSourceManager $sourceManager
     * @param LicenseManager $licenseManager
     * @param ManualImportService $service
     */
    public function __construct(
        FormFactory $factory,
        ExchangeSourceManager $sourceManager,
        LicenseManager $licenseManager,
        ManualImportService $service
    ) {
        parent::__construct($factory);

        $this->sourceManager = $sourceManager;
        $this->licenseManager = $licenseManager;
        $this->service = $service;
    }

    /**
     * Provedu vlasnti import
     * Podle vysledku importu zvolim flash message a presmeruji bud na log nebo na vypis
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues(true);
        $res = $this->service->import($values, $this->getPresenter()->getUser()->id);

        if ($res['logId']) {
            if ($res['hasErrors']) {
                $this->flashMessage('backend.control.import.manual.error', FlashMessageControl::TYPE_ERROR);
            } else {
                $this->flashMessage('backend.control.import.manual.success', FlashMessageControl::TYPE_SUCCESS);
            }

            $this->getPresenter()->redirect(':Admin:Import:logs', ['id' => $res['logId']]);
        } else {
            $this->flashMessage('backend.control.import.manual.null');
            $this->getPresenter()->redirect(':Admin:Import:default');
        }
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

        return $form;
    }

    /**
     * @param Form $form
     */
    protected function appendControls(Form $form)
    {
        $sources = $this->sourceManager->findAll([["[" . IMapper::ID . "] != %i", ExchangeSourceManager::DEFAULT_ID]]) ?: [];
        $sources = Forms::toSelect($sources, 'id', function($source) {
            return "{$source['title']} ({$source['format']})";
        });

        $form->addSelect('source_id', 'backend.control.import.label.source', $sources)->setRequired();

        $licenses = $this->licenseManager->findAll() ?: [];
        $licenses = Forms::toSelect($licenses, 'id', 'title');

        $form->addSelect('license_id', 'backend.control.import.label.license', $licenses)->setRequired()
            ->setDefaultValue(LicenseManager::DEFAULT_ID);

        $form->addText('url', 'backend.control.import.label.url');
        $form->addUpload('file', 'backend.control.import.label.file');
        $form->addCheckbox('certified', 'backend.control.import.label.certified');

        $form['url']->addConditionOn($form['file'], Form::BLANK)
            ->addRule(Form::FILLED, 'backend.control.import.requiredInfo');
        $form['file']->addConditionOn($form['url'], Form::BLANK)
            ->addRule(Form::FILLED, 'backend.control.import.requiredInfo');

        $form->addSubmit('submit', 'backend.control.import.label.process');
    }

    /**
     * Vykresleni komponenty.
     */
    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }
}
