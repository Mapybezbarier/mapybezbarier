<?php

namespace MP\Exchange\Export;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IExpoter
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function export(array $data);
}
