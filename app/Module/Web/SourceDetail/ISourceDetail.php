<?php

namespace MP\Module\SourceDetail;

/**
 * Rozhrani pro pripravu dat pro detail objektu na frontendu z konkretniho zdroje
 */
interface ISourceDetail
{
    /**
     * @param array $object
     * @return array
     */
    public function prepareSourceData($object);
}
