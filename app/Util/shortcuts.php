<?php
/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
if (!function_exists('bdump')) {

    /**
     * Tracy\Debugger::barDump() shortcut.
     *
     * @tracySkipLocation
     *
     * @param mixed $var
     *
     * @return mixed
     */
    function bdump($var)
    {
        foreach (func_get_args() as $arg) {
            Tracy\Debugger::barDump($arg);
        }

        return $var;
    }
}

if (!function_exists('dumpex')) {

    /**
     * Tracy\Debugger::dump(); exit; shortcut.
     *
     * @tracySkipLocation
     *
     * @param $var
     */
    function dumpex($var)
    {
        foreach (func_get_args() as $arg) {
            Tracy\Debugger::dump($arg);
        }

        exit;
    }
}

if (!function_exists('sdump')) {

    /**
     * MP\Util\DibiDumper::dump(); exit; shortcut.
     *
     * @tracySkipLocation
     *
     * @param $var
     */
    function sdump($var)
    {
        foreach (func_get_args() as $arg) {
            \MP\Util\DibiDumper::dump($arg);
        }

        exit;
    }
}
