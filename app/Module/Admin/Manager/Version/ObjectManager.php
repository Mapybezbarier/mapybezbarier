<?php

namespace MP\Module\Admin\Manager\Version;

use MP\Manager\AbstractEnumManager;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectManager extends AbstractEnumManager
{
    /**
     * @param int $id
     */
    public function revertObject($id)
    {
        $this->mapper->revert($id);
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function splitObject($id)
    {
        return $this->mapper->split($id);
    }
}
