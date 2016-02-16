<?php

namespace MP\Manager\Resolver;
use MP\Mapper\IMapper;

/**
 * Resolver ciselnikovych hodnot pro nastaveni automatickeho importu.
 */
class ImportEnumValueResolver extends AbstractEnumValueResolver
{
    /**
     * @return array
     */
    protected function getEnumColumnTableMapping()
    {
        return [
            'source' => 'exchange_source',
            'user' => 'user',
        ];
    }

    /**
     * @override pro uzivatele chci login
     *
     * @param array|null $results
     * @param string $column
     *
     * @return array
     */
    protected function getEnumTitles($results, $column)
    {
        $ret = [];

        if ('user' === $column) {
            foreach ($results as $result) {
                $ret[$result[IMapper::ID]] = "{$result['login']}";
            }
        } else {
            $ret = parent::getEnumTitles($results, $column);
        }

        return $ret;
    }
}
