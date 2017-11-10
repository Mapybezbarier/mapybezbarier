<?php

namespace MP\Module\Web\Component\MarkersControl;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IMarkersControlFactory
{
    /**
     * @return MarkersControl
     */
    public function create(): MarkersControl;
}
