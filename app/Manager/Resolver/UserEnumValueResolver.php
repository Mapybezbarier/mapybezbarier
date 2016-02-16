<?php

namespace MP\Manager\Resolver;
use MP\Mapper\IMapper;

/**
 * Resolver ciselnikovych hodnot pro uzivatele.
 */
class UserEnumValueResolver extends AbstractEnumValueResolver
{
    /**
     * @return array
     */
    protected function getEnumColumnTableMapping()
    {
        return [
            'role' => 'role',
            'license' => 'license',
        ];
    }

    /**
     * @override pro roli chci pair_key
     *
     * @param array|null $results
     * @param string $column
     *
     * @return array
     */
    protected function getEnumTitles($results, $column)
    {
        $ret = [];

        if ('role' === $column) {
            foreach ($results as $result) {
                $ret[$result[IMapper::ID]] = "{$result['pair_key']}";
            }
        } else {
            $ret = parent::getEnumTitles($results, $column);
        }

        return $ret;
    }
}
