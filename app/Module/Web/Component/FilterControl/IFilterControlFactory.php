<?php
/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */

namespace MP\Module\Web\Component\FilterControl;

/**
 * Generovana tovarna pro FilterControl
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IFilterControlFactory
{
    /**
     * @return FilterControl
     */
    public function create();
}
