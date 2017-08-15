<?php

namespace MP\Component\Form\Control;

use MP\Util\Arrays;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\DateTime;
use Nette\Utils\Html;

/**
 * Formularovy provek pro zadani data.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DateInput extends TextInput
{
    /** @const Format data */
    const FORMAT = 'Y-m-d';

    /**
     * @param string|null $label
     */
    public function __construct($label = null)
    {
        parent::__construct($label);

        $this->control->type = 'date';
    }

    /**
     * @return DateTime|null
     */
    public function getValue()
    {
        if (!empty($this->value)) {
            $value = DateTime::from(parent::getValue())->setTime(0, 0);
        } else {
            $value = parent::getValue() ?: null;
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return \Nette\Forms\Controls\TextBase
     */
    public function setValue($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format(self::FORMAT);
        } else if (is_array($value)) {
            $value = Arrays::get($value, 'date', time());
            $value = DateTime::from($value)->format(self::FORMAT);
        } else if ($value) {
            $value = DateTime::from($value)->format(self::FORMAT);
        }

        return parent::setValue($value);
    }

    /**
     * @override Pridani validace
     *
     * @param null|string $caption
     *
     * @return Html|void
     */
    public function getControl($caption = null)
    {
        $this->addCondition(Form::FILLED)->addRule(Form::PATTERN, 'messages.form.error.date', '(19|20)\d{2}\-(0?[1-9]|1[0-2])\-(0?[1-9]|1\d|2\d|3[01])');

        return parent::getControl($caption);
    }
}
