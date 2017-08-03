<?php

namespace MP\Util\WebLoader;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class LoaderFactory extends \WebLoader\Nette\LoaderFactory
{
    /**
     * @override Pouziti vlastniho JavaScriptLoaderu.
     *
     * @param string $name
     *
     * @return JavaScriptLoader
     */
    public function createJavaScriptLoader($name)
    {
        $parentJavaScriptLoader = parent::createJavaScriptLoader($name);

        return new JavaScriptLoader($parentJavaScriptLoader->getCompiler(), $parentJavaScriptLoader->getTempPath());
    }
}
