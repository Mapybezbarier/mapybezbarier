<?php

namespace MP\Module\Admin\Component\LicenseControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Mapper\IMapper;
use MP\Module\Admin\Manager\LicenseManager;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\LogService;
use Nette\Forms\Form;


/**
 * Komponenta pro formular pro vytvoreni nove licence (prosty ciselnik).
 */
class LicenseControl extends AbstractFormControl
{
    /**
     * @persistent
     * @var int
     */
    public $id;

    /**
     * @var LicenseManager
     */
    protected $manager;
    /**
     * @var LogService
     */
    protected $logService;

    /**
     * @param FormFactory $factory
     * @param LicenseManager $manager
     * @param LogService $logService
     */
    public function __construct(FormFactory $factory, LicenseManager $manager, LogService $logService)
    {
        parent::__construct($factory);

        $this->manager = $manager;
        $this->logService = $logService;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param Form $form
     */
    public function processForm(Form $form)
    {
        $values = $form->getValues(true);

        if ($this->id) {
            $values[IMapper::ID] = $this->id;
            $this->logService->log(Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_LICENSE_EDIT, $this->id);
        } else {
            $this->logService->log(Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_LICENSE_CREATE);
        }

        $values = $this->manager->persist($values);
        $this->getPresenter()->redirect(':Admin:Import:license');
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
        $form->addText('title', 'backend.control.license.label.title')->setRequired(true);
        $form->addText('url', 'backend.control.license.label.url');
        $form->addSubmit('submit', 'backend.control.license.label.submit');

        $form->setDefaults($this->prepareValues($form));
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    private function prepareValues(Form $form)
    {
        $values = [];

        if ($this->id) {
            $license = $this->manager->findOneById($this->id);

            if ($license) {
                /** @var \Nette\ComponentModel\IComponent $component */
                foreach ($form->getComponents() as $component) {
                    $name = $component->getName();

                    if (isset($license[$name])) {
                        $values[$name] = $license[$name];
                    }
                }
            } else {
                throw new \Nette\Application\BadRequestException("Unknow license with ID '{$this->id}'");
            }
        }

        return $values;
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
