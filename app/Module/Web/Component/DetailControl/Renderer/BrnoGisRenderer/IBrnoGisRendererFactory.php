<?php

namespace MP\Module\Web\Component\DetailControl\Renderer\BrnoGisRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna pro BrnoGisRenderer.
 */
interface IBrnoGisRendererFactory extends IRendererFactory
{
    /**
     * @param array $object
     *
     * @return BrnoGisRenderer
     */
    public function create(array $object);
}
