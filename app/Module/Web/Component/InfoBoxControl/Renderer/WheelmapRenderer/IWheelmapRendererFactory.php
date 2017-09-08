<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\WheelmapRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna pro WheelmapRenderer.
 *
 * @author Jakub Vrbas
 */
interface IWheelmapRendererFactory extends IRendererFactory
{
    /**
     * @param array $object
     *
     * @return WheelmapRenderer
     */
    public function create(array $object);
}
