<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IRenderer extends \MP\Module\Web\Component\IRenderer
{
    public function renderDetail();

    /**
     * @param bool $embedded
     */
    public function setEmbedded($embedded);
}
