<?php

namespace MP\Manager\Resolver;
use MP\Object\ObjectMetadata;

/**
 *  Resolver ciselnikovych hodnot pro rampu/liziny mapoveho objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class RampSkidsEnumValueResolver extends AbstractEnumValueResolver
{
    /**
     * @return array
     */
    protected function getEnumColumnTableMapping()
    {
        return ObjectMetadata::$ENUM_COLUMN_TABLE_MAPPING[ObjectMetadata::RAMP_SKIDS];
    }
}
