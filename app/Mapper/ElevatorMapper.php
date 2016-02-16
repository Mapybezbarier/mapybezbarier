<?php

namespace MP\Mapper;

use MP\Object\ObjectMetadata;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ElevatorMapper extends AbstractLangAwareDatabaseMapper
{
    /**
     * @return string
     */
    protected function getDataKeyColumn()
    {
        return 'elevator_id';
    }

    /**
     * @return string[]
     */
    protected function getLangAwareColumns()
    {
        return ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::ELEVATOR];
    }
}
