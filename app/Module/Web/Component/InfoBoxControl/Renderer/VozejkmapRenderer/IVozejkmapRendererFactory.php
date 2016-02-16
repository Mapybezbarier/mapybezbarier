<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\VozejkmapRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna pro VozejkmapRenderer.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IVozejkmapRendererFactory extends IRendererFactory
{
    /**
     * @param array $object
     *
     * @return VozejkmapRenderer
     */
    public function create(array $object);
}
