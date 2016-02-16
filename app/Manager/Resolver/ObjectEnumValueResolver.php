<?php

namespace MP\Manager\Resolver;
use MP\Object\ObjectMetadata;

/**
 * Resolver ciselnikovych hodnot pro mapovy objekt.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectEnumValueResolver extends AbstractEnumValueResolver
{
    /**
     * @return array
     */
    protected function getEnumColumnTableMapping()
    {
        return ObjectMetadata::$ENUM_COLUMN_TABLE_MAPPING[ObjectMetadata::OBJECT];
    }
}
