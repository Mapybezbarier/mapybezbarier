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
                $restrictions = ["[user_id] = %i", $this->user->getId()];
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
}
