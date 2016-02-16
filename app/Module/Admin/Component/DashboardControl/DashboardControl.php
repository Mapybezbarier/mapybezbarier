<?php

namespace MP\Module\Admin\Component\DashboardControl;

use MP\Component\AbstractControl;
use MP\Module\Admin\Service\Authorizator;

/**
 * Dashboard pro navigaci v backendu
 */
class DashboardControl extends AbstractControl
{
    public function render()
    {
        $template = $this->getTemplate();
        $template->modules = $this->prepareModules();
        $template->user = $this->getPresenter()->getUser();
        $template->render();
    }

    /**
     * Podle ACL sestavi mozne odkazy dashboardu
     * @return array
     */
    protected function prepareModules()
    {
        $ret = [
            Authorizator::RESOURCE_OBJECT => [
                'defaultAction' => Authorizator::ACTION_VIEW,
                'links' => [
                    Authorizator::ACTION_VIEW,
                    Authorizator::ACTION_SELECT,
                ]
            ],
            Authorizator::RESOURCE_USER => [
                'defaultAction' => Authorizator::ACTION_VIEW,
                'links' => [
                    Authorizator::ACTION_VIEW,
                    Authorizator::ACTION_CREATE,
                ]
            ],
            Authorizator::RESOURCE_IMPORT => [
                'defaultAction' => Authorizator::ACTION_VIEW,
                'links' => [
                    Authorizator::ACTION_VIEW,
                    Authorizator::ACTION_LOGS,
                ]
            ],
            Authorizator::RESOURCE_SYSTEM => [
                'defaultAction' => Authorizator::ACTION_VIEW,
                'links' => [
                    Authorizator::ACTION_VIEW,
                    Authorizator::ACTION_BACKUP,
                ]
            ],
        ];

        return $ret;
    }
}
