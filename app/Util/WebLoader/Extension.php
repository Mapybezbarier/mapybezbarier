<?php

namespace MP\Util\WebLoader;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Extension extends \WebLoader\Nette\Extension
{
    /**
     * @override Zmena tridy factory
     */
    public function loadConfiguration()
    {
        parent::loadConfiguration();

        $definition = $this->getContainerBuilder()->getDefinition($this->prefix('factory'));
        $definition->setClass(LoaderFactory::class, $definition->getFactory()->arguments);
    }
}
