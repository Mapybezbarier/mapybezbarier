<?php

namespace MP\Mapper;

use MP\Object\ObjectMetadata;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class RampSkidsMapper extends AbstractLangAwareDatabaseMapper
{
    /**
     * @return string
     */
    protected function getDataKeyColumn()
    {
        return 'rampskids_id';
    }

    /**
     * @return string[]
     */
    protected function getLangAwareColumns()
    {
        return ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::RAMP_SKIDS];
    }
}
