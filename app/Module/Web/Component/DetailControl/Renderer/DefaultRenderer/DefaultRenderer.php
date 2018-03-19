<?php

namespace MP\Module\Web\Component\DetailControl\Renderer\DefaultRenderer;

use IPub\Images\TImages;
use MP\Module\Web\Component\AbstractRenderer;
use Nette\Application\UI\ITemplate;

/**
 * Renderer obsahu detailu mapoveho objektu z internich formatu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DefaultRenderer extends AbstractRenderer
{
    use TImages;

    /**
     * @param ITemplate $template
     *
     * @throws \IPub\Images\Exceptions\InvalidArgumentException
     * @throws \IPub\Images\Exceptions\InvalidStateException
     */
    protected function prepareTemplateVars(ITemplate $template)
    {
        parent::prepareTemplateVars($template);

        $template->imageSrc = null;

        if ($this->object['image']) {
            $template->imageSrc = $this->imgHelpers->imageLink([
                'provider' => 'presenter',
                'storage' => 'images',
                'namespace' => 'object',
                'filename' => basename($this->object['image']),
                'size' => '560',
                'algorithm' => 'fit',
            ]);
        }
    }
}
