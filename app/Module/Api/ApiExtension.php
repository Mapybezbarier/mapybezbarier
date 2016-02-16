<?php

namespace MP\Module\Api;

use Flame\Modules\Providers\IRouterProvider;
use MP\DI\ModuleExtension;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Utils\Strings;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ApiExtension extends ModuleExtension implements IRouterProvider
{
    /**
     * @return IRouter
     */
    public function getRoutesDefinition()
    {
        $name = $this->getName();

        $prefix = $this->locale . '/' . Strings::lower($name) . '/v1';

        $routeList = new RouteList($name);
        $routeList[] = new Route("{$prefix}/objects/<format>", [
            'presenter' => 'Object',
            'action' => 'objects'
        ]);

        return $routeList;
    }
}
