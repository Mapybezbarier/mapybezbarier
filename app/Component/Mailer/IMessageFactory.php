<?php

namespace MP\Component\Mailer;

use Nette\Mail\Message;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
interface IMessageFactory
{
    /** @const */
    const FROM = 'from',
        TO = 'to',
        SUBJECT = 'subject',
        BODY = 'body',
        DATA = 'data';

    /**
     * @param array $message
     * @return Message
     */
    public function create(array $message);
}
