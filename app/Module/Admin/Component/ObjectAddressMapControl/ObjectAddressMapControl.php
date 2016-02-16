<?php

namespace MP\Module\Admin\Component\ObjectAddressMapControl;
use MP\Component\AbstractControl;

/**
 * Komponenta pro manualni vyber GPS souradnic objektu bez adresy.
 */
class ObjectAddressMapControl extends AbstractControl
{
    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }
}
