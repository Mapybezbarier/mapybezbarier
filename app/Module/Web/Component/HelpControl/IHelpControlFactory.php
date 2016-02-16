<?php

namespace MP\Module\Web\Component;

/**
 * Generovana tovarna pro HelpControl.
 */
interface IHelpControlFactory
{
    /**
     * @return HelpControl
     */
    public function create();
}
