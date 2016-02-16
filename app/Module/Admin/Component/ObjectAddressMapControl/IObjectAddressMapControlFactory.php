<?php

namespace MP\Module\Admin\Component\ObjectAddressMapControl;

/**
 * Generovana tovarna na IObjectAddressMapControl.
 */
interface IObjectAddressMapControlFactory
{
    /**
     * @return ObjectAddressMapControl
     */
    public function create();
}
