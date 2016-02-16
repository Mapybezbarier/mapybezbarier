<?php

namespace MP\Module\Admin\Component\DashboardControl;

/**
 * Generovana tovarna na DashboardControl.
 */
interface IDashboardControlFactory
{
    /**
     * @return DashboardControl
     */
    public function create();
}
