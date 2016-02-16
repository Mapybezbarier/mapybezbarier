<?php

namespace MP\Module\Admin\Presenters;

/**
 * Uvodni strana prihlaseneho uzivatele
 */
class DashboardPresenter extends AbstractAuthorizedPresenter
{
    /**
     * @override Na uvodni strane nechci dashboard v zahlavi - je vypsan primo v tele
     */
    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->preventDashboard = true;
    }
}
