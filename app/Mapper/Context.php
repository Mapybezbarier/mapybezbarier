<?php

namespace MP\Mapper;

use Nette\Object;

/**
 * Globalni kontext mapperu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Context extends Object
{
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
