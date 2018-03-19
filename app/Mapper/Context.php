<?php

namespace MP\Mapper;

use Nette\SmartObject;

/**
 * Globalni kontext mapperu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Context
{
    use SmartObject;

    /** @var bool Zda ma pri vyberu dat dojit k mergi jazykovych dat */
    protected $mergeLanguageData = true;

    /**
     * @return boolean
     */
    public function mergeLanguageData()
    {
        return $this->mergeLanguageData;
    }

    /**
     * @param boolean $mergeLanguageData
     */
    public function setMergeLanguageData($mergeLanguageData)
    {
        $this->mergeLanguageData = $mergeLanguageData;
    }
}
