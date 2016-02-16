<?php

namespace MP\Component;

/**
 * Generovana tovarna na FlashMessageControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IFlashMessageControlFactory
{
    /**
     * @return FlashMessageControl
     */
    public function create();
}
