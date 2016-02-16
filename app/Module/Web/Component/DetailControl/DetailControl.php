<?php

namespace MP\Module\Web\Component\DetailControl;

use MP\Component\AbstractControl;
use MP\Module\SourceDetail\DetailService;
use MP\Module\Web\Component\InfoBoxControl\IInfoBoxControlFactory;
use MP\Module\Web\Component\InfoBoxControl\InfoBoxControl;
use MP\Module\Web\Component\IRenderer;
use MP\Module\Web\Component\IRendererFactory;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DetailControl extends AbstractControl
{
    /** @var DetailService */
    protected $detailService;

    /** @var IInfoBoxControlFactory */
    protected $infoBoxFactory;

    /** @var IRendererFactory */
    protected $rendererFactory;

    /** @var array */
    protected $object;

    /**
     * @param DetailService $detailService
     * @param IInfoBoxControlFactory $infoBoxFactory
     * @param IRendererFactory $rendererFactory
     */
    public function __construct(DetailService $detailService, IInfoBoxControlFactory $infoBoxFactory, IRendererFactory $rendererFactory)
    {
        $this->detailService = $detailService;
        $this->infoBoxFactory = $infoBoxFactory;
        $this->rendererFactory = $rendererFactory;
    }

    public function render()
    {
        if ($this->object) {
            $template = $this->getTemplate();
            $template->object = $this->object;
            $template->render();
        }
    }

    /**
     * @param array $object
     */
    public function setObject(array $object)
    {
        $this->object = $this->detailService->getDetail($object);
    }

    /**
     * @return InfoBoxControl
     */
    protected function createComponentInfoBox()
    {
        $control = $this->infoBoxFactory->create();
        $control->setObject($this->object);

        return $control;
    }

    /**
     * @return IRenderer
     */
    protected function createComponentRenderer()
    {
        $renderer = $this->rendererFactory->create($this->object);

        return $renderer;
    }
}
