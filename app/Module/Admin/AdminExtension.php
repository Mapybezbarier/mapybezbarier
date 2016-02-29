<?php

namespace MP\Module\Admin;

use Flame\Modules\Providers\IRouterProvider;
use MP\DI\ModuleExtension;
use MP\Util\RuntimeMode;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Utils\Strings;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class AdminExtension extends ModuleExtension implements IRouterProvider
{
    /** @var RuntimeMode @inject */
    public $runtimeMode;

    /**
     * @return IRouter
     */
    public function getRoutesDefinition()
    {
        $name = $this->getName();

        $prefix = Strings::lower($name);

        $routeList = new RouteList($name);

        $routeList[] = new Route("{$prefix}", [
            'presenter' => 'Dashboard',
            'action' => 'default',
            'id' => null
        ]);

        $routeList[] = new Route("{$prefix}/login", [
            'presenter' => 'Access',
            'action' => 'login'
        ]);

        $routeList[] = new Route("{$prefix}/registration", [
            'presenter' => 'Access',
            'action' => 'registration'
        ]);

        $routeList[] = new Route("{$prefix}/reset-password", [
            'presenter' => 'Access',
            'action' => 'reset'
        ]);

        $routeList[] = new Route("{$prefix}/change-password", [
            'presenter' => 'Access',
            'action' => 'change'
        ]);

        $routeList[] = new Route("{$prefix}/object/draft/<id>[/<mapping>]", [
            'presenter' => 'Draft',
            'action' => 'default'
        ]);

        $routeList[] = new Route("{$prefix}/object/compare/<id>", [
            'presenter' => 'Compare',
            'action' => 'default'
        ]);

        $routeList[] = new Route("{$prefix}/object[/<action>][/<id>]", [
            'presenter' => 'Object',
            'action' => 'default',
            'id' => null
        ]);

        $routeList[] = new Route("{$prefix}/user[/<action>][/<id>]", [
            'presenter' => 'User',
            'action' => 'default',
            'id' => null
        ]);
        $routeList[] = new Route("{$prefix}/import[/<action>][/<id>]", [
            'presenter' => 'Import',
            'action' => 'default',
            'id' => null
        ]);
        $routeList[] = new Route("{$prefix}/system[/<action>][/<id>]", [
            'presenter' => 'System',
            'action' => 'default',
            'id' => null
        ]);

        return $routeList;
    }
}
