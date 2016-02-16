<?php

namespace MP\Module\Web\Component\DetailControl\Renderer\DefaultRenderer;

use MP\Module\Web\Component\IRendererFactory;

/**
 * Generovana tovarna na DefaultRenderer.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IDefaultRendererFactory extends IRendererFactory
{
    /**
     * @param array $object
     *
     * @return DefaultRenderer
     */
    public function create(array $object);
}
