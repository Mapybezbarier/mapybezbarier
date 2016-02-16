<?php

namespace MP\Module\Web\Presenters;

use MP\Module\Web\Component\GAControl;
use MP\Module\Web\Component\IGAControlFactory;
use MP\Presenters\AbstractPresenter;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractWebPresenter extends AbstractPresenter
{
    /**
     * @persistent
     * @var string
     */
    public $locale;

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->locale = $this->locale;
    }

    /**
     * @param IGAControlFactory $factory
     *
     * @return GAControl
     */
    protected function createComponentGoogleAnalytics(IGAControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
