<?php

namespace MP\Module\Admin\Component\ImportLogsList;

use MP\Component\AbstractControl;
use MP\Module\Admin\Service\ImportLogService;

/**
 * Komponenta pro vykresleni seznamu logu automatickych i manualnich importu.
 */
class ImportLogsListControl extends AbstractControl
{
    /** @var ImportLogService */
    protected $service;

    /**
     * @param ImportLogService $service
     */
    public function __construct(ImportLogService $service)
    {
        $this->service = $service;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->items = $this->service->findListData();
        $template->render();
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function renderDetail($id)
    {
        $template = $this->getTemplate('.detail');
        $template->setParameters($this->service->prepareLog($id));

        if (!$template->log && !$template->count) {
            throw new \Nette\Application\BadRequestException;
        }

        $template->render();
    }
}
