<?php

namespace MP\Component;

use MP\Util\Lang\TTranslator;

/**
 * Komponenta pro vykresleni flash message.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FlashMessageControl extends AbstractControl
{
    use TTranslator;

    /** Typ zpravy pro info message */
    const TYPE_INFO = 'info';
    /** Typ zpravy pro success message */
    const TYPE_SUCCESS = 'success';
    /** Typ zpravy pro warning message */
    const TYPE_WARNING = 'warning';
    /** Typ zpravy pro error message */
    const TYPE_ERROR = 'error';

    public function render()
    {
        $template = $this->getTemplate();
        $template->messages = $this->getMessages();
        $template->render();
    }

    /**
     * @return array
     */
    protected function getMessages()
    {
        $messages = [];

        $template = $this->getPresenter()->getTemplate();

        if (isset($template->flashes)) {
            foreach ($template->flashes as $message) {
                $message->message = $this->autoTranslate($message->message);

                $messages[] = $message;
            }
        }

        return $messages;
    }
}
