<?php

namespace MP\Module\Web\Component\MapControl;

use MP\Component\AbstractControl;
use MP\Module\Web\Component\InfoBoxControl\IInfoBoxControlFactory;
use MP\Module\Web\Component\InfoBoxControl\InfoBoxControl;
use Nette\Application\Responses\TextResponse;
use Nette\Http\IRequest;
use Nette\Utils\Json;

/**
 * Komponenta pro vykresleni mapy.
 */
class MapControl extends AbstractControl
{
    /** @const GET parametry pro konfiguraci mapy. */
    const GET_CENTER_LAT = 'center-lat',
        GET_CENTER_LNG = 'center-lng',
        GET_ZOOM = 'zoom',
        GET_MAPS = 'maps';

    /** @const Nazev komponenty pro info box. */
    const COMPONENT_INFO_BOX = 'infoBox';

    /** @var IInfoBoxControlFactory */
    protected $infoBoxFactory;

    /** @var IRequest */
    protected $request;

    /** @var bool vykreslovano jako embedded mapa? */
    protected $embedded = false;

    /**
     * @param IInfoBoxControlFactory $infoBoxFactory
     * @param IRequest $request
     */
    public function __construct(
        IInfoBoxControlFactory $infoBoxFactory,
        IRequest $request
    ) {
        $this->infoBoxFactory = $infoBoxFactory;
        $this->request = $request;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->config = $this->prepareConfig();
        $template->render();
    }

    /**
     * @param bool $embedded
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = $embedded;
    }

    /**
     * @param int[] $ids
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleInfoBox(array $ids)
    {
        if ($this->getPresenter()->isAjax()) {
            /** @var InfoBoxControl $infoBoxControl */
            $infoBoxControl = $this[self::COMPONENT_INFO_BOX];
            $infoBoxControl->setIds($ids);

            $response = new TextResponse($infoBoxControl->toString());

            $this->getPresenter()->sendResponse($response);
        } else {
            throw new \Nette\Application\BadRequestException;
        }
    }

    /**
     * @return InfoBoxControl
     */
    protected function createComponentInfoBox()
    {
        $control = $this->infoBoxFactory->create();
        $control->setEmbedded($this->embedded);

        return $control;
    }

    /**
     * Pripravi konfiguraci mapy.
     *
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    private function prepareConfig()
    {
        // Cesko -> lat 49.5, lng 14.9, zoom 8
        // Konto Bariery -> lat 50.085, lng 14.42, zoom 15
        $config = [
            'center' => [
                'lat' => (float) $this->request->getQuery(self::GET_CENTER_LAT, 50.085),
                'lng' => (float) $this->request->getQuery(self::GET_CENTER_LNG, 14.42),
            ],
            'streetViewControl' => false, //pouze pro google maps
            'zoomControl' => false, //pouze pro google maps
            'mapTypeControl' => false, //pouze pro google maps
            'zoom' => (int) $this->request->getQuery(self::GET_ZOOM, 16)
        ];

        return Json::encode($config);
    }
}
