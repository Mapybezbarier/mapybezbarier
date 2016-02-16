<?php

namespace MP\Module\Admin\Component\UserList;

/**
 * Generovana tovarna na UserListControl.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IUserListControlFactory
{
    /**
     * @return UserListControl
     */
    public function create();
}
