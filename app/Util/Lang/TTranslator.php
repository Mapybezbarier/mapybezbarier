<?php

namespace MP\Util\Lang;

use Kdyby\Translation\Translator;
use MP\Util\Strings;

/**
 * Traita pro pouziti v komponentach a sluzbach.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
trait TTranslator
{
    /** @var Translator */
    protected $translator;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Zkratka pro volani $this->translator->translate()
     *
     * @see Kdyby\Translation\Translator::translate
     *
     * @return string transalted message
     */
    protected function translate()
    {
        return call_user_func_array([$this->translator, 'translate'], func_get_args());
    }

    /**
     * Automaticky zdetekuje, zdali string zacina na "messages." - pokud ano, prozene ho
     * translatorem na translate. Pokud ne, vraci tak jak je - predchazi tomu, aby se nestalo, ze se
     * (napr. ve formularich) zavoal translate dvakrat - v pripade neprelozeneho stringu by odseklo dve
     * casti domeny message ID
     *
     * @return string
     */
    protected function autoTranslate()
    {
        $message = null;
        $args = func_get_args();

        // vybalime v pripade, ze je zabalene pole - volalo se autoTranslate(func_get_args())
        if (1 === count($args) && is_array(reset($args))) {
            $args = reset($args);
        }

        if (0 < count($args)) {
            $message = reset($args);

            $translatable = false;

            foreach ($this->translator->getCatalogue()->getDomains() as $domain) {
                $translatable |= Strings::startsWith($message, "{$domain}.");
            }

            if ($translatable) {
                $message = call_user_func_array([$this->translator, 'translate'], $args);
            }
        }

        return $message;
    }

}
