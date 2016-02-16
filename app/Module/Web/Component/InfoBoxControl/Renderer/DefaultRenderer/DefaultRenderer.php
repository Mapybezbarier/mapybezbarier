<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\DefaultRenderer;

use MP\Module\Web\Component\InfoBoxControl\Renderer\AbstractRenderer;

/**
 * Renderer obsahu info boxu mapoveho objektu z internich formatu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DefaultRenderer extends AbstractRenderer
{
    public function renderDetail()
    {
        $template = $this->getTemplate('.detail');

        $this->prepareTemplateVars($template);

        $template->render();
    }
}
