<?php

namespace MP\Util;

use Nette\DI\Container;

/**
 * Helper pro praci s behovym prostredim.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class RuntimeMode
{
    /** @var Container */
    protected $context;

    /**
     * @param Container $context
     */
    public function __construct(Container $context)
    {
        $this->context = $context;
    }

    /**
     * Vrati, zda je zapnut debug mode.
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return (bool) Arrays::get($this->context->getParameters(), 'debugMode', false);
    }

    /**
     * Vrati, zda aplikace bezi na testovacim serveru.
     *
     * @return bool
     */
    public function isStagingMode()
    {
        return file_exists(APP_DIR . '/config/config.test.neon');
    }
}
