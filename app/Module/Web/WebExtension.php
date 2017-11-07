<?php

namespace MP\Module\Web;

use Flame\Modules\Providers\IRouterProvider;
use MP\DI\ModuleExtension;
use MP\Util\RuntimeMode;
use Nette\Application\IRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class WebExtension extends ModuleExtension implements IRouterProvider
{
    /** @var RuntimeMode @inject */
    public $runtimeMode;

    /**
     * @return IRouter
     */
    public function getRoutesDefinition()
    {
        $routeList = new RouteList($this->getName());

        $routeList[] = new Route('/', [
            'presenter' => 'Homepage',
            'action' => 'default',
            'locale' => 'cs',
        ], Route::ONE_WAY);

        $routeList[] = new Route("{$this->locale}[/<action>]?id=<id [0-9]+>", [
            'presenter' => 'Homepage',
            'action' => [
                Route::VALUE => 'default',
                Route::FILTER_TABLE => [
                    $this->translator->translate('messages.router.embeddedInfo') => 'embeddedInfo',
                    $this->translator->translate('messages.router.exportInfo') => 'exportInfo',
                    $this->translator->translate('messages.router.help') => 'help',
                ],
                Route::FILTER_STRICT => true,
            ],
        ]);
        $routeList[] = new Route("{$this->locale}/embedded", [
            'presenter' => 'Embedded',
            'action' => 'default',
        ]);
        $routeList[] = new Route("{$this->locale}/stats", [
            'presenter' => 'Stats',
            'action' => 'default',
        ]);

        $routeList[] = new Route("/cron/<action>", [
            'presenter' => 'Cron',
            'action' => 'default',
        ]);

        if ($this->runtimeMode->isDebugMode()) {
            $routeList[] = new Route('/parse-dpa', [
                'presenter' => 'Homepage',
                'action' => 'parseDpa',
            ]);
        }

        return $routeList;
    }
}
