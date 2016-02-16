<?php

namespace MP\Module\Web\Component\MapControl;

/**
 * Generovana tovarna pro MapControl
 */
interface IMapControlFactory
{
    /**
     * @return MapControl
     */
    public function create();
}
