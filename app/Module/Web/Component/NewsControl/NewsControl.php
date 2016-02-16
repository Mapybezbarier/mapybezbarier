<?php

namespace MP\Module\Web\Component;

use MP\Component\AbstractControl;
use MP\Util\Lang\Lang;
use Nette\Http\SessionSection;

/**
 * Komponenta pro vykresleni novinek
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class NewsControl extends AbstractControl
{
    /** @var Lang */
    protected $lang;

    /** @var SessionSection */
    protected $session;

    /**
     * @param Lang $lang
     * @param SessionSection $session
     */
    public function __construct(Lang $lang, SessionSection $session)
    {
        $this->lang = $lang;
        $this->session = $session;
    }

    public function render()
    {
        if ($this->isRenderable()) {
            $template = $this->getTemplate();
            $template->locale = $this->lang->getLocale();
            $template->render();

            $this->session->rendered = true;
        }
    }

    /**
     * @return bool
     */
    protected function isRenderable()
    {
        $isRenderable = true;

        if (true === $this->session->rendered || !file_exists(ASSET_DIR . "/iframe/iframe.{$this->lang->getLocale()}.html")) {
            $isRenderable = false;
        }

        return $isRenderable;
    }
}
