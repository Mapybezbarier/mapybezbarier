<?php

namespace MP\Module\Admin\Service;

use MP\Mapper\IMapper;
use MP\Util\Arrays;
use Nette\Security\User;

/**
 * Sestavuje restrictor pro vypis uzivatelu
 */
class UserRestrictorBuilder
{
    /** @const Nazvy restrikci */
    const RESTRICTION_TITLE = 'title';
    const RESTRICTION_ROLE = 'roleId';

    /**
     * @param User $user
     * @param array $restrictions
     *
     * @return array
     */
    public function getRestrictor(User $user, array $restrictions = [])
    {
        $restrictor = [];

        $this->prepareDefaultRestrictor($restrictor, $user);
        $this->prepareFilterRestrictor($restrictor, $restrictions);

        return $restrictor;
    }

    /**
     * ACL
     *
     * @param array $restrictor
     * @param User $user
     *
     * @return array
     */
    protected function prepareDefaultRestrictor(array &$restrictor, User $user)
    {
        if ($user->isInRole(Authorizator::ROLE_AGENCY)) {
            $restrictor[] = [
                '%or',
                [
                    ["[" . IMapper::ID . "] = %i", $user->getId()],
                    ["[parent_id] = %i", $user->getId()]
                ],
            ];
        } else if ($user->isInRole(Authorizator::ROLE_MAPPER)) {
            $restrictor[] = ["[" . IMapper::ID . "] = %i", $user->getId()];
        }

        return $restrictor;
    }

    /**
     * @param array $restrictor
     * @param array $restrictions
     */
    protected function prepareFilterRestrictor(array &$restrictor, array $restrictions)
    {
        if ($title = Arrays::get($restrictions, self::RESTRICTION_TITLE, null)) {
            $restrictor[] = [
                "%or", [
                    ["[login] ILIKE %~like~", $title],
                    ["[firstname] ILIKE %~like~", $title],
                    ["[surname] ILIKE %~like~", $title],
                    ["[ic_title] ILIKE %~like~", $title],
                ]
            ];
        }

        if ($roleId = Arrays::get($restrictions, self::RESTRICTION_ROLE, null)) {
            $restrictor[] = ["[role_id] = %i", $roleId];
        }
    }
}
