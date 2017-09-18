<?php

namespace MP\Module\Admin\Component\LicenseListControl;

use Dibi\ForeignKeyConstraintViolationException;
use MP\Component\AbstractControl;
use MP\Component\FlashMessageControl;
use MP\Manager\LicenseManager;
use MP\Module\Admin\Service\Authorizator;
use MP\Module\Admin\Service\LogService;


/**
 * Komponenta pro vykresleni seznamu nastavenych automatickych importu.
 */
class LicenseListControl extends AbstractControl
{
    /** @var LicenseManager */
    protected $manager;
    /**
     * @var LogService
     */
    protected $logService;

    /**
     * @param LicenseManager $manager
     * @param LogService $logService
     */
    public function __construct(LicenseManager $manager, LogService $logService)
    {
        $this->manager = $manager;
        $this->logService = $logService;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->items = $this->manager->findAll();
        $template->render();
    }

    /**
     * Pokusi se smazat licenci, ale nejprve overi, zda se nekde nepouziva
     * @param int $id
     * @throws \Nette\Application\BadRequestException
     */
    public function handleDelete($id)
    {
        $item = $this->manager->findOneBy([['[id] = %i', $id]]);

        if ($item) {
            try {
                $this->manager->remove($id);
                $this->logService->log(Authorizator::RESOURCE_IMPORT, LogService::ACTION_IMPORT_LICENSE_DELETE, $id);
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->flashMessage('backend.control.license.deleteError', FlashMessageControl::TYPE_ERROR);
            }
        } else {
            throw new \Nette\Application\BadRequestException;
        }

        $this->redirect('this');
    }
}
