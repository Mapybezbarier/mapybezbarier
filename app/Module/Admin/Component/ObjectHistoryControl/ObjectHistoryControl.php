<?php

namespace MP\Module\Admin\Component\ObjectHistoryControl;

use MP\Component\AbstractControl;
use MP\Component\FlashMessageControl;
use MP\Module\Admin\Service\ObjectHistoryService;
use Nette\Localization\ITranslator;

/**
 * Komponenta pro vykresleni historie objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectHistoryControl extends AbstractControl
{
    /** @var ObjectHistoryService */
    protected $historyService;

    /** @var ITranslator */
    protected $translator;

    /**
     * @param ObjectHistoryService $historyService
     * @param ITranslator $translator
     */
    public function __construct(ObjectHistoryService $historyService, ITranslator $translator)
    {
        $this->historyService = $historyService;
        $this->translator = $translator;
    }

    /**
     * @param array $object
     */
    public function render(array $object)
    {
        $template = $this->getTemplate();
        $template->current = $this->prepareVersion($object);
        $template->history = $this->prepareHistory($object);
        $template->render();
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleSplit($id)
    {
        if ($object = $this->historyService->getObject($id)) {
            $id = $this->historyService->splitObject($object);

            $this->flashMessage('backend.object.flash.split.success', FlashMessageControl::TYPE_SUCCESS);

            $this->getPresenter()->redirect(':Admin:Object:history', ['id' => $id]);
        } else {
            throw new \Nette\Application\BadRequestException;
        }
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleRevert($id)
    {
        if ($object = $this->historyService->getObject($id)) {
            $this->historyService->revertObject($object);

            $this->flashMessage('backend.object.flash.revert.success', FlashMessageControl::TYPE_SUCCESS);

            $this->redirect('this');
        } else {
            throw new \Nette\Application\BadRequestException;
        }
    }

    /**
     * @param array $object
     *
     * @return array
     */
    protected function prepareHistory(array $object)
    {
        $history = $this->historyService->getObjects($object);

        foreach ($history as &$version) {
            $this->prepareVersion($version);
        }

        return $history;
    }

    /**
     * @param array $version
     *
     * @return array
     */
    protected function prepareVersion(array &$version)
    {
        $version['mapping_date'] = $version['mapping_date']->format($this->translator->translate('backend.format.date'));
        $version['modified_date'] = $version['modified_date']->format($this->translator->translate('backend.format.datetime'));

        return $version;
    }
}
