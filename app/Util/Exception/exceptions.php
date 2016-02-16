<?php

namespace MP\Exception;

/**
 * Traita pro nastaveni vlastni zpravu vyjimce - vypise pak na vystup misto klasicke 500
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>

 */
trait TExceptionMessage
{
    protected $customMessage;

    /**
     * Nastavi vlastni chybu
     *
     * @param string $customMessage
     *
     * @return $this
     */
    public function setCustomMessage($customMessage)
    {
        $this->customMessage = $customMessage;

        return $this;
    }

    /**
     * Vraci vlastni zpravu chyby
     *
     * @return string|null
     */
    public function getCustomMessage()
    {
        return $this->customMessage;
    }
}

/**
 * Class LogicException - base exception class for all logical (bad usage, method call etc.)
 */
class LogicException extends \LogicException
{
    use TExceptionMessage;
}

/**
 * Class RuntimeException - base exception class for all runtime errors (unexpected error that cannot be prevented)
 */
class RuntimeException extends \RuntimeException
{
    use TExceptionMessage;
}
