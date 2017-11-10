<?php

namespace MP\Module\Web\Presenters;

use MP\Module\Web\Component\MapControl\IMapControlFactory;
use MP\Module\Web\Component\MapControl\MapControl;
use MP\Module\Web\Component\MarkersControl\IMarkersControlFactory;
use MP\Module\Web\Component\MarkersControl\MarkersControl;
use MP\Util\Arrays;
use MP\Util\WebLoader\JavaScriptLoader;
use WebLoader\FileCollection;

/**
 * Traita pridavajici podporu pro vykresleni mapovych podkladu od Google a Seznamu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
trait TMapPresenter
{
    /**
     * @persistent
     * @var bool
     */
    public $maps = false;

    /**
     * @override Pridani JavaScriptovych souboru podle zvolenych mapovych podkladu
     *
     * @return JavaScriptLoader
     * @throws \Nette\InvalidArgumentException
     */
    protected function createComponentJs()
    {
        $control = parent::createComponentJs();

        /** @var FileCollection $collection */
        $collection = $control->getCompiler()->getFileCollection();

        if ($this->maps) {
            $apiKey = Arrays::get($this->context->getParameters(), ['google', 'mapApiKey']);

            $collection->addRemoteFile("//maps.googleapis.com/maps/api/js?key=$apiKey&libraries=places&language=cs");
            $collection->addFile('gmaps.js');
        } else {
            $collection->addRemoteFile('//api.mapy.cz/loader.js');
            $collection->addRemoteFile('!Loader.load();');
            $collection->addFile('mapycz.js');
        }

        $collection->addFile('map.js');

        return $control;
    }

    public function handleMarkers()
    {
        $this['markers']->setRenderable(true);

        $this->redrawControl('markers');
    }

    /**
     * @param IMapControlFactory $factory
     *
     * @return MapControl
     */
    protected function createComponentMap(IMapControlFactory $factory)
    {
        return $factory->create();
    }

    /**
     * @param IMarkersControlFactory $factory
     *
     * @return MarkersControl
     */
    protected function createComponentMarkers(IMarkersControlFactory $factory)
    {
        return $factory->create();
    }
}
