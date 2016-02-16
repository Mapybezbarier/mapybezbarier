<?php

namespace MP\Manager\Resolver;
use MP\Object\ObjectMetadata;

/**
 * Resolver ciselnikovych hodnot pro vytah mapoveho objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ElevatorEnumValueResolver extends AbstractEnumValueResolver
{
    /**
     * @return array
     */
    protected function getEnumColumnTableMapping()
    {
        return ObjectMetadata::$ENUM_COLUMN_TABLE_MAPPING[ObjectMetadata::ELEVATOR];
    }
}
