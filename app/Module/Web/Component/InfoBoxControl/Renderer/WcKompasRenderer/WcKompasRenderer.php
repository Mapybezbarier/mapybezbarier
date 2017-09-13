<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\WcKompasRenderer;

use MP\Module\Web\Component\InfoBoxControl\Renderer\AbstractRenderer;

/**
 * Renderer obsahu info boxu mapoveho objektu z WC Kompas.
 *
 * @author Jakub Vrbas
 */
class WcKompasRenderer extends AbstractRenderer
{
    public function renderDetail()
    {
        $template = $this->getTemplate('.detail');

        $this->prepareTemplateVars($template);

        $template->render();
    }
}
