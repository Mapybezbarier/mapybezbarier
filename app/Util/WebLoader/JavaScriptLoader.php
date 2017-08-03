<?php

namespace MP\Util\WebLoader;


use MP\Util\Strings;
use Nette\Utils\Html;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class JavaScriptLoader extends \WebLoader\Nette\JavaScriptLoader
{
    /**
     * @override Pokud zacina $source !, pak jej vypisu jako kus cisteho JavaScriptu
     *
     * @param string $source
     *
     * @return Html
     */
    public function getElement($source)
    {
        if (Strings::startsWith($source, '!')) {
            $element = Html::el('script')->setText(Strings::trim($source, '!'))->type('text/javascript');
        } else {
            $element = parent::getElement($source);
        }

        return $element;
    }
}
