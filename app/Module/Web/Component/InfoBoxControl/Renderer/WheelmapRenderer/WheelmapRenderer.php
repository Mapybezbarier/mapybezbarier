<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\WheelmapRenderer;

use MP\Module\Web\Component\InfoBoxControl\Renderer\AbstractRenderer;

/**
 * Renderer obsahu info boxu mapoveho objektu z Wheelmap.
 *
 * @author Jakub Vrbas
 */
class WheelmapRenderer extends AbstractRenderer
{
    public function renderDetail()
    {
        $template = $this->getTemplate('.detail');

        $this->prepareTemplateVars($template);

        $template->render();
    }
}
