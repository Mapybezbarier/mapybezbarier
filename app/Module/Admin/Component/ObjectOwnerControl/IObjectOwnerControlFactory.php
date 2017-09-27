<?php

namespace MP\Module\Admin\Component\ObjectOwnerControl;

/**
 * Generovana tovarna na ObjectOwnerControl.
 */
interface IObjectOwnerControlFactory
{
    /**
     * @return ObjectOwnerControl
     */
    public function create();
}
