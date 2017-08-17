<?php

namespace MP\Module\Web\Component\DetailControl\Renderer\WheelmapRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna pro WheelmapRenderer.
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
