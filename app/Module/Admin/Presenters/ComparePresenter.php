<?php

namespace MP\Module\Admin\Presenters;

use MP\Module\Admin\Component\ObjectCompareControl\IObjectCompareControlFactory;
use MP\Module\Admin\Component\ObjectCompareControl\ObjectCompareControl;
use MP\Module\Admin\Service\ObjectHistoryService;

/**
 * Presenter pro porovnani objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ComparePresenter extends AbstractObjectPresenter
{
    /** @const Nazev komponenty pro porovnani objektu */
    const COMPONENT_COMPARE = 'compare';

    /** @var ObjectHistoryService @inject */
    public $historySevice;

    /** @var array */
    protected $version;

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDefault($id)
    {
        $this->version = $this->prepareVersion($id);

        $this[self::COMPONENT_COMPARE]->setVersion($this->version);
    }

    /**
     * @param int $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function renderDefault($id)
    {
        $this->template->version = $this->version;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \Nette\Application\BadRequestException
     */
    protected function prepareVersion($id)
    {
        $version = $this->historySevice->getObject($id);

        if (!$version) {
            throw new \Nette\Application\BadRequestException("Version with ID '{$id}' not found");
        }

        return $version;
    }

    /**
     * @param IObjectCompareControlFactory $factory
     *
     * @return ObjectCompareControl
     */
    protected function createComponentCompare(IObjectCompareControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
