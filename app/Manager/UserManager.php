<?php

namespace MP\Manager;

use MP\Util\Strings;

/**
 * Manazer uzivatelu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class UserManager extends AbstractEnumManager
{
    /**
     * @override E-mail se uklada s malymi pismeny bez mezer.
     *
     * @param array $values
     *
     * @return array
     */
    public function persist(array $values)
    {
        if (isset($values['login'])) {
            $values['login'] = Strings::lower($values['login']);
            $values['login'] = Strings::trim($values['login']);
        }

        if (isset($values['email'])) {
            $values['email'] = Strings::lower($values['email']);
            $values['email'] = Strings::trim($values['email']);
        }

        return parent::persist($values);
    }
}
