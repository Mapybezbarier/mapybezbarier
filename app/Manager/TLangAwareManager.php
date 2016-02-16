<?php

namespace MP\Manager;

use MP\Mapper\AbstractLangAwareDatabaseMapper;
use Nette\Utils\Validators;

/**
 * Implementace rozhranni ILangAwareManager.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
trait TLangAwareManager
{
    /**
     * @param int $id
     * @param string $lang
     * @param array $data
     *
     * @throws \Nette\Utils\AssertionException
     */
    public function persistLangData($id, $lang, array $data)
    {
        Validators::assert($this->mapper, AbstractLangAwareDatabaseMapper::class);

        $this->mapper->saveLangData($id, $lang, $data);
    }
}
