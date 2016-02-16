<?php

namespace MP\Module\Admin\Component\PaginatorControl;

/**
 * Generovana tovarna na PaginatorControl
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IPaginatorControlFactory
{
    /**
     * @return PaginatorControl
     */
    public function create();
}
