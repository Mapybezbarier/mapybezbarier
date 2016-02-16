<?php

namespace MP\Module\Web\Component;

/**
 * Rozhranni tovaren na renderery objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IRendererFactory
{
    /**
     * @param array $object
     *
     * @return IRenderer
     */
    public function create(array $object);
}
