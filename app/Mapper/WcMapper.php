<?php

namespace MP\Mapper;

use MP\Object\ObjectMetadata;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class WcMapper extends AbstractLangAwareDatabaseMapper
{
    /**
     * @return string
     */
    protected function getDataKeyColumn()
    {
        return 'wc_id';
    }

    /**
     * @return string[]
     */
    protected function getLangAwareColumns()
    {
        return ObjectMetadata::$LANG_AWARE_COLUMNS[ObjectMetadata::WC];
    }
}
