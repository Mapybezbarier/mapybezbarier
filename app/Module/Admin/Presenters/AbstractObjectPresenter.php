<?php

namespace MP\Module\Admin\Presenters;

/**
 * Predek presenteru se akcemi nad objekty.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractObjectPresenter extends AbstractAuthorizedPresenter
{
    /**
     * @persistent
     * @var string
     */
    public $lang = 'cs';

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->lang = $this->lang;
    }
}
