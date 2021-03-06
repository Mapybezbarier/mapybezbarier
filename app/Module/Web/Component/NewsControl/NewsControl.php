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

    /** @var bool */
    private $iframeReady;

    /**
     * @param Lang $lang
     * @param SessionSection $session
     * @param bool $iframeReady
     */
    public function __construct(Lang $lang, SessionSection $session, bool $iframeReady)
    {
        $this->lang = $lang;
        $this->session = $session;
        $this->iframeReady = $iframeReady;
    }

    public function render()
    {
        if ($this->isRenderable()) {
            $this->setRenderable(false);

            $template = $this->getTemplate();
            $template->locale = $this->lang->getLocale();
            $template->render();
        }
    }

    /**
     * @param bool $isRenderable
     */
    public function setRenderable(bool $isRenderable)
    {
        $this->session->rendered = false === $isRenderable;
    }

    /**
     * @return bool
     */
    protected function isRenderable()
    {
        $isRenderable = true;

        if (true === $this->session->rendered || !file_exists(ASSET_DIR . "/iframe/iframe.{$this->lang->getLocale()}.html") || !$this->iframeReady) {
            $isRenderable = false;
        }

        return $isRenderable;
    }
}
