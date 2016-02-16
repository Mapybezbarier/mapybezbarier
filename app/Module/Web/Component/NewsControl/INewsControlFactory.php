<?php

namespace MP\Module\Web\Component;

/**
 * Generovana tovarna na NewsControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface INewsControlFactory
{
    /**
     * @return NewsControl
     */
    public function create();
}
