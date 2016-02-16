<?php

namespace MP\Module\Web\Presenters;

use MP\Manager\ObjectManager;

/**
 * Presenter pro statistiky o mapovani
 * Urceno k vypisu prostrednictvim iframe na materskem webu - nema tedy standardni layout
 */
class StatsPresenter extends AbstractWebPresenter
{
    /** @var ObjectManager @inject */
    public $manager;

    public function actionDefault()
    {
        $this->getHttpResponse()->setHeader('X-Frame-Options', NULL);
        $this->template->regions = $this->manager->getRegionsStats();
        $this->template->totalCount = 0;

        foreach ($this->template->regions as $region) {
            $this->template->totalCount += $region['count'];
        }
    }
}
