<?php

namespace MP\Component\Form\Control;

use Nette\Utils\Html;

/**
 * Vlastni implementace RadioList pro moznost lepsiho formatovani vystupu
 */
class RadioList extends \Nette\Forms\Controls\RadioList
{
    /**
     * @param  string  $label
     * @param  array   $items options from which to choose
     */
    public function __construct($label = NULL, array $items = NULL)
    {
        parent::__construct($label, $items);

        $this->separator = Html::el('div', ['class' => 'rg_item']);
    }

    /**
     * @override Nastaveni spolecne message ID pro validaci.
     *
     * @return Html
     */
    public function getControl()
    {
        $this->setAttribute('data-lfv-message-id', $this->getHtmlId() . '_message');

        return parent::getControl();
    }
}
