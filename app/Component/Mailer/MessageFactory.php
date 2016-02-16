<?php

namespace MP\Component\Mailer;

use MP\Util\Arrays;
use MP\Util\Classes;
use MP\Util\Lang\TTranslator;
use MP\Util\Strings;
use Nette\Localization\ITranslator;
use Nette\Mail\Message;
use Nette\Utils\Callback;

/**
 * Tovarna na zpravu maileru.
 *
 * Konfiguraci v poli prevadi na Message.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class MessageFactory implements IMessageFactory
{
    use TTranslator;

    /** @var array */
    protected $settings = [];

    /**
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;

        $this->init();
    }

    /**
     * Inicizalice tridy.
     */
    protected function init()
    {
        $this->settings = [
            IMessageFactory::FROM => ADMIN_MAIL,
            IMessageFactory::TO => ADMIN_MAIL,
            IMessageFactory::SUBJECT => 'messages.control.mailer.subject',
        ];
    }

    /**
     * Z konfigurace zpravy vytvori Message.
     *
     * Konfigurace muze obsahovat klice viz. konstanty IMessageFactory.
     *
     * @param array $messageSettings
     *
     * @return Message
     */
    public function create(array $messageSettings)
    {
        $message = new Message();

        $messageSettings = array_merge($this->settings, $messageSettings);

        foreach ($messageSettings as $key => $value) {
            $fixedKey = Strings::firstUpper($key);
            $isArrayCall = false;

            if (Strings::endsWith($fixedKey, "[]")) {
                if (Arrays::isIterable($value)) { // konci to na [] -> ocekavam pole
                    $fixedKey = Strings::substring($fixedKey, 0, -2); // odriznem koncove []
                    $isArrayCall = true;
                } else {
                    throw new \Nette\InvalidArgumentException("Value of default message settings with key {$key} must be iterable, got " . Classes::getType($value));
                }
            }

            $setMethod = "set{$fixedKey}"; // from => setFrom
            $addMethod = "add{$fixedKey}"; // to => addTo

            if (method_exists($message, $setMethod) && is_callable([$message, $setMethod])) {
                $method = $setMethod;
            } elseif (method_exists($message, $addMethod) && is_callable([$message, $addMethod])) {
                $method = $addMethod;
            } else { // neznamy klic -> muze se handlovat rucne -> pokracujem
                continue;
            }

            if ($isArrayCall) {
                foreach ($value as $callValue) {
                    Callback::invokeArgs([$message, $method], (array) $this->autoTranslate($callValue));
                }
            } else {
                Callback::invokeArgs([$message, $method], (array) $this->autoTranslate($value));
            }
        }

        return $message;
    }
}
