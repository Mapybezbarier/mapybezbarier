<?php

namespace MP\Module\Web\Component\InfoBoxControl;

/**
 * Generovana tovarna na InfoBoxControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IInfoBoxControlFactory
{
    /**
     * @return InfoBoxControl
     */
    public function create();
}
