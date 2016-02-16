<?php

namespace MP\Module\Admin\Component\AutomaticImportControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Manager\ExchangeSourceManager;
use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\LicenseManager;
use MP\Module\Admin\Service\AutomaticImportService;
use MP\Util\Forms;
use Nette\Forms\Form;

/**
 * Komponenta pro formular pro zalozeni noveho automatickeho importu.
 */
class AutomaticImportControl extends AbstractFormControl
{
    /** @var ExchangeSourceManager */
    protected $sourceManager;

    /** @var AutomaticImportService */
    protected $service;

    /**
     * @var LicenseManager
     */
    protected $licenseManager;

    /**
     * @param FormFactory $factory
     * @param ExchangeSourceManager $sourceManager
     * @param AutomaticImportService $service
     * @param LicenseManager $licenseManager
     */
    public function __construct(
        FormFactory $factory,
        ExchangeSourceManager $sourceManager,
        AutomaticImportService $service,
        LicenseManager $licenseManager
    ) {
        parent::__construct($factory);

        $this->sourceManager = $sourceManager;
        $this->service = $service;
        $this->licenseManager = $licenseManager;
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues(true);
        $values['user_id'] = $this->getPresenter()->getUser()->id;

        $this->service->create($values);

        $this->getPresenter()->redirect(':Admin:Import:default');
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

        $form->addText('url', 'backend.control.import.label.url')->setRequired();
        $form->addText('hours_offset', 'backend.control.import.label.hours_offset')
            ->setRequired()->addRule(Form::INTEGER);
        $form->addCheckbox('certified', 'backend.control.import.label.certified');

        $form->addSubmit('submit', 'backend.control.import.label.submit');
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
