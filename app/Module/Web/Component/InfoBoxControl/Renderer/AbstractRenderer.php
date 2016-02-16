<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer;

use Nette\Application\UI\ITemplate;

/**
 * Predek komponent pro vykresleni obsahu info boxu mapoveho markeru.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractRenderer extends \MP\Module\Web\Component\AbstractRenderer implements IRenderer
{
    /** @var bool */
    protected $embedded = false;

    /**
     * Vykresleni info boxu v detailu.
     */
    abstract public function renderDetail();

    /**
     * @param bool $embedded
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = $embedded;
    }

    /**
     * @param ITemplate $template
     */
    protected function prepareTemplateVars(ITemplate $template)
    {
        parent::prepareTemplateVars($template);

        $template->embedded = $this->embedded;
    }
}
