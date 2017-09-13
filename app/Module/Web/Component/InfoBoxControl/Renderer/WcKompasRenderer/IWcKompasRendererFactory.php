<?php

namespace MP\Module\Web\Component\InfoBoxControl\Renderer\WcKompasRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna pro WcKompasRenderer.
 *
 * @author Jakub Vrbas
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
