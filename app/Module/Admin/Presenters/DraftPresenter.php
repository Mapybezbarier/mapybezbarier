<?php

namespace MP\Module\Admin\Presenters;

use MP\Module\Admin\Component\ObjectControl\IObjectControlFactory;
use MP\Module\Admin\Component\ObjectControl\ObjectControl;
use MP\Module\Admin\Service\ObjectDraftService;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DraftPresenter extends AbstractObjectPresenter
{
    /** @const Nazev komponenty s formularem objektu */
    const COMPONENT_OBJECT = 'object';

    /** @var ObjectDraftService @inject */
    public $draftService;

    /** @var array */
    protected $draft;

    /**
     * @param int $id
     * @param bool $mapping
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDefault($id, $mapping = false)
    {
        $this->draft = $this->prepareDraft($id);

        $this[self::COMPONENT_OBJECT]->setDraft($this->draft);
        $this[self::COMPONENT_OBJECT]->setMapping($mapping);
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function renderDefault($id)
    {
        $this->template->draft = $this->draft;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \Nette\Application\BadRequestException
     */
    protected function prepareDraft($id)
    {
        $draft = $this->draftService->getDraft($id);

        if ($draft) {
            $this->checkOwnership($draft);
        } else {
            throw new \Nette\Application\BadRequestException("Draft with ID '{$id}' not found");
        }

        return $draft;
    }

    /**
     * @param IObjectControlFactory $factory
     *
     * @return ObjectControl
     */
    protected function createComponentObject(IObjectControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
