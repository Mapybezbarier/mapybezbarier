<?php

namespace MP\DI;

use Kdyby\Translation\Translator;

/**
 * Extenze modulu
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class ModuleExtension extends CompilerExtension
{
    /** @var Translator @inject */
    public $translator;

    /** @var string */
    protected $locale = '/<locale cs|en>';

    /**
     * @return string
     */
    public function getName()
    {
        $parts = explode('\\', static::class);

        return ucfirst(str_replace('Extension', '', end($parts)));
    }
}
