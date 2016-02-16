<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\VozejkmapRenderer;

use MP\Module\Web\Component\InfoBoxControl\Renderer\AbstractRenderer;

/**
 * Renderer obsahu info boxu mapoveho objektu z Vozejkmap.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class VozejkmapRenderer extends AbstractRenderer
{
    public function renderDetail()
    {
        $template = $this->getTemplate('.detail');

        $this->prepareTemplateVars($template);

        $template->render();
    }
}
