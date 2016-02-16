<?php

namespace MP\Module\Admin\Component\AutomaticImportList;

use MP\Component\AbstractControl;
use MP\Module\Admin\Manager\AutomaticImportManager;
use MP\Module\Admin\Service\AutomaticImportService;


/**
 * Komponenta pro vykresleni seznamu nastavenych automatickych importu.
 */
class AutomaticImportListControl extends AbstractControl
{
    /** @var AutomaticImportManager */
    protected $manager;
    /**
     * @var AutomaticImportService
     */
    protected $service;

    /**
     * @param AutomaticImportManager $manager
     * @param AutomaticImportService $service
     */
    public function __construct(AutomaticImportManager $manager, AutomaticImportService $service)
    {
        $this->manager = $manager;
        $this->service = $service;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->items = $this->manager->findAll();
        $template->render();
    }

    /**
     * @param int $id
     * @throws \Nette\Application\BadRequestException
     */
    public function handleDelete($id)
    {
        $item = $this->manager->findOneBy([['[id] = %i', $id]]);

        if ($item) {
            $this->service->delete($id);
        } else {
            throw new \Nette\Application\BadRequestException;
        }

        $this->redrawControl('list');
    }
}
