<?php

namespace MP\Module\Web\Presenters;

use MP\Module\Web\Component\MapControl\IMapControlFactory;
use MP\Module\Web\Component\MapControl\MapControl;
use MP\Module\Web\Component\MarkersControl\IMarkersControlFactory;
use MP\Module\Web\Component\MarkersControl\MarkersControl;
use MP\Module\Web\Service\ObjectRestrictorBuilder;

/**
 * Presenter pro mapu vkladanou pres iFrame
 * Funkcne rozsiruje Homepage o moznost nastavit stred a uroven priblizeni mapy
 * Oproti Homepage nema layout, jen mapu s markery
 */
class EmbeddedPresenter extends AbstractWebPresenter
{
    use TMapPresenter;

    /** @var ObjectRestrictorBuilder @inject */
    public $restrictorBuilder;

    public function actionDefault()
    {
        $this->getHttpResponse()->setHeader('X-Frame-Options', null);
    }

    /**
     * @param IMapControlFactory $factory
     *
     * @return MapControl
     */
    protected function createComponentMap(IMapControlFactory $factory)
    {
        $control = $factory->create();
        $control->setEmbedded(true);

        return $control;
    }

    /**
     * @param IMarkersControlFactory $factory
     *
     * @return MarkersControl
     */
    protected function createComponentMarkers(IMarkersControlFactory $factory)
    {
        $restrictions = $this->getHttpRequest()->getQuery();

        $this->restrictorBuilder->prepareRestrictions($restrictions, true);

        $control = $factory->create();
        $control->setRestrictor($this->restrictorBuilder->getRestrictor());

        return $control;
    }
}
