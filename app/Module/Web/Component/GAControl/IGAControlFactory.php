<?php

namespace MP\Module\Web\Component;

/**
 * Generovana tovarna pro GAControl.
 */
interface IGAControlFactory
{
    /**
     * @return GAControl
     */
    public function create();
}
