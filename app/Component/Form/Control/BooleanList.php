<?php

namespace MP\Component\Form\Control;

use MP\Util\Strings;

/**
 * Formularovy prvek pro hodnoty ano/ne
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class BooleanList extends RadioList
{
    /**
     * @override Pretypovani vstupu na int
     * @param scalar
     * @return self
     */
    public function setValue($value)
    {
        if ($this->checkAllowedValues && $value !== NULL && !array_key_exists((int) $value, $this->items)) {
            $set = Strings::truncate(implode(', ', array_map(function ($s) { return var_export($s, TRUE); }, array_keys($this->items))), 70, '...');
            throw new \Nette\InvalidArgumentException("Value '$value' is out of allowed set [$set] in field '{$this->name}'.");
        }

        $this->value = $value === NULL ? NULL : key(array((int) $value => NULL));

        return $this;
    }

    /**
     * @override Pretypovani na bool
     *
     * @return bool|null
     */
    public function getValue()
    {
        $value = parent::getValue();

        return $value === NULL ? NULL : (bool) $value;
    }
}
