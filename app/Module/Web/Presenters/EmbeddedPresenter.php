<?php

namespace MP\Module\Web\Presenters;

use MP\Module\Web\Component\MapControl\IMapControlFactory;
use MP\Module\Web\Component\MapControl\MapControl;
use MP\Module\Web\Service\ObjectRestrictorBuilder;

/**
 * Presenter pro mapu vkladanou pres iFrame
 * Funkcne rozsiruje Homepage o moznost nastavit stred a uroven priblizeni mapy
 * Oproti Homepage nema layout, jen mapu s markery
 */
class EmbeddedPresenter extends AbstractWebPresenter
{
    /** @var ObjectRestrictorBuilder @inject */
    public $restrictorBuilder;

    public function actionDefault()
    {
        $this->getHttpResponse()->setHeader('X-Frame-Options', NULL);
    }

    /**
     * @param IMapControlFactory $factory
     *
     * @return MapControl
     */
    protected function createComponentMap(IMapControlFactory $factory)
    {
        $restrictions = $this->getHttpRequest()->getQuery();

        $this->restrictorBuilder->prepareRestrictions($restrictions, true);

        $control = $factory->create();
        $control->setRestrictor($this->restrictorBuilder->getRestrictor());
        $control->setEmbedded(true);

        return $control;
    }
}
