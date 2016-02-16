<?php

namespace MP\Module\Web\Component\NavigationControl;

use MP\Component\AbstractControl;
use MP\Util\Arrays;
use MP\Util\Lang\Lang;

/**
 * Komponenta pro vykresleni menu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class NavigationControl extends AbstractControl
{
    /** @var Lang */
    protected $lang;

    /** @var array */
    protected $items;

    /**
     * @param Lang $lang
     * @param array $items
     */
    public function __construct(Lang $lang, array $items)
    {
        $this->lang = $lang;
        $this->items = $items;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->items = Arrays::get($this->items, $this->lang->getLocale(), []);
        $template->render();
    }
}
