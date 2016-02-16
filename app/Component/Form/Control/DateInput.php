<?php

namespace MP\Component\Form\Control;

use MP\Util\Arrays;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\IControl;
use Nette\Utils\DateTime;

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

        $this->addRule(__CLASS__ . '::validateDate', 'messages.form.error.date', DateTime::from(time())->format(self::FORMAT));
    }

    /**
     * @return DateTime|null
     */
    public function getValue()
    {
        return self::validateDate($this) ? DateTime::from(parent::getValue())->setTime(0, 0) : parent::getValue();
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
     * @param IControl $control
     *
     * @return bool
     */
    public static function validateDate(IControl $control)
    {
        $valid = true;

        if ($control->rawValue) {
            DateTime::createFromFormat(self::FORMAT, $control->rawValue);

            $valid = (0 == DateTime::getLastErrors()["error_count"] && 0 == DateTime::getLastErrors()["warning_count"]);
        }

        return $valid;
    }
}
