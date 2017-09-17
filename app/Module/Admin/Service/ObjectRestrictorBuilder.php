<?php

namespace MP\Module\Admin\Service;

use MP\Util\Arrays;
use Nette\Security\User;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectRestrictorBuilder extends \MP\Service\ObjectRestrictorBuilder
{
    /** @const Klice dostupnych restrikci */
    const RESTRICTION_TERM = 'term',
        RESTRICTION_USER = 'user';

    /** @var UserService */
    protected $userService;

    /** @var User */
    protected $user;

    /**
     * @param UserService $userService
     * @param User $user
     */
    public function __construct(UserService $userService, User $user)
    {
        $this->userService = $userService;
        $this->user = $user;
    }

    /**
     * @param array $restrictions
     *
     * @return array|null
     */
    public function getRestrictor(array $restrictions = [])
    {
        $restrictor = [];

        $user = Arrays::get($restrictions, self::RESTRICTION_USER, null);

        $restrictor[] = $this->prepareUserRestrictions($user);

        if ($term = Arrays::get($restrictions, self::RESTRICTION_TERM, null)) {
            $restrictor[] = $this->prepareTermRestrictions($term);
        }

        if ($accessibility = Arrays::get($restrictions, self::RESTRICTION_ACCESSIBILITY, [])) {
            $restrictor[] = $this->prepareAccessibilityRestrictions($accessibility);
        }

        if ($categories = Arrays::get($restrictions, self::RESTRICTION_CATEGORY, [])) {
            $restrictor[] = $this->prepareCategoryRestrictions($categories);
        }

        if ($types = Arrays::get($restrictions, self::RESTRICTION_TYPE, [])) {
            $restrictor[] = $this->prepareTypeRestrictions($types);
        }

        $restrictor[] = $this->prepareEditableRestrictions();

        $restrictor = array_filter($restrictor);

        return $restrictor ?: null;
    }

    /**
     * @param int|null $user
     *
     * @return array
     */
    protected function prepareUserRestrictions($user)
    {
        $restrictions = [];

        if (null === $user) {
            if ($this->user->isInRole(Authorizator::ROLE_AGENCY)) {
                $restrictions = [
                    '%or',
                    [
                        ["[user_id] = %i", $this->user->getId()],
                        ["EXISTS (
                            SELECT 1
                            FROM [user]
                            WHERE
                                [user].[parent_id] = %i", $this->user->getId(), "
                                AND [user].[id] = [user_id]
                        )"]
                    ]
                ];
            } else if ($this->user->isInRole(Authorizator::ROLE_MAPPER)) {
                $user = $this->userService->getUser($this->user->getId(), true);

                if ($agency = Arrays::get($user, 'parent_id', null)) {
                    $restrictions = [
                        '%or',
                        [
                            ["[user_id] = %i", $this->user->getId()],
                            ["[user_id] = %i", $agency]
                        ]
                    ];
                } else {
                    $restrictions = ["[user_id] = %i", $this->user->getId()];
                }
            }
        } else if ($user) {
            $restrictions = ["[user_id] = %i", $user];
        }

        return $restrictions;
    }

    /**
     * @param string $term
     *
     * @return array
     */
    protected function prepareTermRestrictions($term)
    {
        $term = strtr($term, ['_' => "\\_", '%' => "\\%"]);
        $term = [sprintf("('%%' || remove_diacritics(%%s) || '%%')"), $term];

        $restrictions[] = [
            "
                EXISTS (
                    SELECT 1
                    FROM [map_object_lang]
                    WHERE
                        [search_title] ILIKE %sql", $term, "
                        AND [map_object_id] = [id]
                )
            ",
        ];

        return $restrictions;
    }

    /**
     * Zobrazovat pouze objekty z editovatelnych zdroju
     * Ve sprave objektu v administraci se nepracuje s importovanymi daty z externich zdroju
     * @return array
     */
    protected function prepareEditableRestrictions()
    {
        $restrictions[] = [
            "
                EXISTS (
                    SELECT 1
                    FROM [exchange_source]
                    WHERE
                        [exchange_source].[editable]
                        AND [source_id] = [exchange_source].[id]
                )
            ",
        ];

        return $restrictions;
    }

}
