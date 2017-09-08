<?php

namespace MP\Module\Web\Component\DetailControl\Renderer\WcKompasRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna pro WcKompasRenderer.
 */
interface IWcKompasRendererFactory extends IRendererFactory
{
    /**
     * @param array $object
     *
     * @return WcKompasRenderer
     */
    public function create(array $object);
}
