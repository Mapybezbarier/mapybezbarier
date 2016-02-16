<?php

namespace MP\Mapper;

use MP\Object\ObjectMetadata;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class PlatformMapper extends AbstractLangAwareDatabaseMapper
{
    /**
     * @return string
     */
    protected function getDataKeyColumn()
    {
        return 'platform_id';
    }

    /**
     * @return string[]
     */
    protected function getLangAwareColumns()
    {
        return ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::PLATFORM];
    }
}
