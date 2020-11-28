<?php

namespace MP\Util\Tracy;

/**
 * Upraveny logger Tracy vynucujici zaslani bugreportu s kazdou novou chybou.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Logger extends \Tracy\Logger
{
    /**
     * @override Zasilani e-mailu s kazdou chybou.
     *
     * @param string|\Exception|\Throwable $message
     */
    protected function sendEmail($message): void
    {
        if ($this->email && $this->mailer) {
            call_user_func($this->mailer, $message, implode(', ', (array) $this->email));
        }
    }
}
