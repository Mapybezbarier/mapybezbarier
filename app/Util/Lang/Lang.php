<?php

namespace MP\Util\Lang;

use Kdyby\Translation\Translator;
use Nette\Http\Request;
use Nette\Object;

/**
 * Podpurna trida pro praci s jazkem (uzivalsky nastavenym, detekovanym z browseru apod.)
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Lang extends Object
{
    /** @const Parametr v URI nesouci jazykovou mutaci dat */
    const PARAM_LANG = 'lang';

    /** @var string */
    protected $locale = null;

    /** @var string */
    protected $lang = null;

    /** @var string[] */
    protected $allowed = [];

    /** @var Request */
    protected $request;

    /**
     * @param Request $request
     * @param Translator $translator
     * @param array $allowed
     */
    public function __construct(Request $request, Translator $translator, $allowed = [])
    {
        $this->request = $request;

        $this->locale = $translator->getLocale();
        $this->lang = $this->request->getQuery(self::PARAM_LANG, null);
        $this->allowed = $allowed;
    }

    /**
     * Vrati jazyk pro lokalizaci.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Vrati jazyk pro data. Pokud neni jazyk pro data nastaven, vraci jazyk pro lokalizaci.
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang ?: $this->locale;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param bool $exclusive
     *
     * @return string[]
     */
    public function getAllowed($exclusive = false)
    {
        $allowed = array_combine($this->allowed, $this->allowed);

        if ($exclusive) {
            unset($allowed[$this->getLang()]);
        }

        return $allowed;
    }

    /**
     * @param string[] $allowed
     */
    public function setAllowed($allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * Vola getLocale().
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getLocale();
    }
}
