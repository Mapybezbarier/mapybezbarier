<?php

namespace MP\Module\Web\Component;

use MP\Component\AbstractControl;

/**
 * Komponenta pro vypis napovedy
 */
class HelpControl extends AbstractControl
{
    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }
}
