<?php

namespace MP\Module\Admin\Presenters;

use MP\Util\Arrays;

/**
 * Predek presenteru se akcemi nad objekty.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractObjectPresenter extends AbstractAuthorizedPresenter
{
    /**
     * @persistent
     * @var string
     */
    public $lang = 'cs';

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->lang = $this->lang;
    }

    /**
     * @override Mapar ma pristup k objektu agentury, pod kterou spada
     *
     * @param int $id
     *
     * @return bool
     */
    protected function checkMapperOwnership($id)
    {
        $owner = parent::checkMapperOwnership($id);

        $user = $this->userService->getUser($this->getUser()->getId(), true);

        if ($agency = Arrays::get($user, 'parent_id', null)) {
            $isOwnedByMyAgency = ($id == $agency);
        } else {
            $isOwnedByMyAgency = false;
        }

        $owner = ($owner || $isOwnedByMyAgency);

        return $owner;
    }
}
