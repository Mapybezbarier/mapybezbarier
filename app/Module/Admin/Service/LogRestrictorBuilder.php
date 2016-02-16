<?php

namespace MP\Module\Admin\Service;

use MP\Util\Arrays;
use Nette\Security\User;

/**
 * Sestavuje restrictor pro vypis zaznamu logu akci uzivatelu
 */
class LogRestrictorBuilder
{
    /** @const Nazvy restrikci */
    const RESTRICTION_ACTION = 'actionKey';
    const RESTRICTION_MODULE = 'moduleKey';
    const RESTRICTION_ID = 'changedId';
    const RESTRICTION_USER = 'userId';

    /** @var User */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $restrictions
     *
     * @return array
     */
    public function getRestrictor(array $restrictions = [])
    {
        $restrictor = [];

        $this->prepareDefaultRestrictor($restrictor);
        $this->prepareFilterRestrictor($restrictor, $restrictions);

        return $restrictor;
    }

    /**
     * ACL
     * @param array $restrictor
     * @return array
     */
    protected function prepareDefaultRestrictor(array &$restrictor)
    {
        if ($this->user->isInRole(Authorizator::ROLE_AGENCY)) {
            $restrictor = [
                ['%or',
                    [
                        ["[user_id] = %i", $this->user->getId()],
                        ["[user_id] IN (SELECT [id] FROM [user] WHERE [parent_id] = %i)", $this->user->getId()]
                    ]
                ]
            ];
        } else if ($this->user->isInRole(Authorizator::ROLE_MAPPER)) {
            $restrictor = [["[user_id] = %i", $this->user->getId()]];
        }

        return $restrictor;
    }

    /**
     * @param array $restrictor
     * @param array $restrictions
     */
    protected function prepareFilterRestrictor(array &$restrictor, array $restrictions)
    {
        if ($actionKey = Arrays::get($restrictions, self::RESTRICTION_ACTION, null)) {
            $restrictor[] = ["[action_key] ILIKE %s", $actionKey];
        }

        if ($moduleKey = Arrays::get($restrictions, self::RESTRICTION_MODULE, null)) {
            $restrictor[] = ["[module_key] ILIKE %s", $moduleKey];
        }

        if ($userId = Arrays::get($restrictions, self::RESTRICTION_USER, null)) {
            $restrictor[] = ["[user_id] = %i", $userId];
        }

        if ($changedId = Arrays::get($restrictions, self::RESTRICTION_ID, null)) {
            $restrictor[] = ["[changed_id] = %i", $changedId];
        }
    }
}
