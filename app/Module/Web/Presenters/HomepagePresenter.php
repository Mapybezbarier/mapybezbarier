<?php

namespace MP\Module\Web\Presenters;

use MP\Exchange\Service\DpaTransformator;
use MP\Manager\ObjectManager;
use MP\Module\Web\Component\DetailControl\DetailControl;
use MP\Module\Web\Component\DetailControl\IDetailControlFactory;
use MP\Module\Web\Component\EmbeddedInfoControl;
use MP\Module\Web\Component\ExportControl;
use MP\Module\Web\Component\FilterControl\FilterControl;
use MP\Module\Web\Component\FilterControl\IFilterControlFactory;
use MP\Module\Web\Component\HelpControl;
use MP\Module\Web\Component\IEmbeddedInfoControlFactory;
use MP\Module\Web\Component\IExportControlFactory;
use MP\Module\Web\Component\IHelpControlFactory;
use MP\Module\Web\Component\INewsControlFactory;
use MP\Module\Web\Component\MapControl\IMapControlFactory;
use MP\Module\Web\Component\MapControl\MapControl;
use MP\Module\Web\Component\NavigationControl\INavigationControlFactory;
use MP\Module\Web\Component\NavigationControl\NavigationControl;
use MP\Module\Web\Component\NewsControl;
use MP\Module\Web\Service\ObjectRestrictorBuilder;
use MP\Module\Web\Service\ObjectService;
use MP\Util\Arrays;
use Nette\Application\Responses\TextResponse;
use Nette\Http\IRequest;
use WebLoader\FileCollection;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class HomepagePresenter extends AbstractWebPresenter
{
    /** @const Nazev komponenty popupu s exportem */
    const COMPONENT_EXPORT = 'export';
    /** @const Nazev komponenty popupu pro vlozeni mapy */
    const COMPONENT_EMBEDDED_INFO = 'embeddedInfo';
    /** @const Nazev komponenty popupu s napovedou */
    const COMPONENT_HELP = 'help';
    /** @const Nazev komponenty filtru objektu */
    const COMPONENT_FILTER = 'filter';
    /** @const Nazev komponenty s detailem objektu */
    const COMPONENT_DETAIL = 'detail';
    /** @const Nazev komponenty s mapou */
    const COMPONENT_MAP = 'map';

    /** @const Nazev parametru s jmenem popupu */
    const PARAM_POPUP = 'popup';
    /** @const Nazev parametru s volbou mapove sady */
    const PARAM_MAPS = 'maps';

    /**
     * @persistent
     * @var int
     */
    public $id;

    /**
     * @persistent
     * @var bool
     */
    public $maps = false;

    /** @var ObjectService @inject */
    public $objectService;

    /** @var ObjectManager @inject */
    public $objectManager;

    /** @var ObjectRestrictorBuilder @inject */
    public $objectRestrictorBuilder;

    /** @var DpaTransformator @inject */
    public $dpaTransformator;

    /**
     * @param int|null $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDefault($id)
    {
        $this->id = $id;

        if (null !== $this->id) {
            $object = $this->objectService->getObjectByObjectId($this->id);

            if (!$object) {
                throw new \Nette\Application\BadRequestException("Unknown object with ID '{$this->id}'.");
            }

            $this[self::COMPONENT_DETAIL]->setObject($object);
        }
    }

    public function renderDefault()
    {
        $restrictor = $this->objectRestrictorBuilder->getRestrictor();

        $this[self::COMPONENT_MAP]->setRestrictor($restrictor);

        $this->template->filtered = (bool) $restrictor;
        $this->template->popup = $this->getParameter(self::PARAM_POPUP, null);
        $this->template->maps = $this->getParameter(self::PARAM_MAPS, null);

        if ($this->isAjax() && $this->getHttpRequest()->isMethod(IRequest::POST)) {
            $this->redrawControl('filter');
            $this->redrawControl('markers');
        }
    }

    public function handleDetail()
    {
        $this->redrawControl('detail');
    }

    /**
     * Informace o moznostech exportu s odkazy na export aktualni mnoziny objektu dle filtru
     */
    public function renderExportInfo()
    {
        if (!$this->isAjax()) {
            $this->forward('default', [self::PARAM_POPUP => self::COMPONENT_EXPORT]);
        } else {
            /** @var ExportControl $control */
            $control = $this[self::COMPONENT_EXPORT];

            $response = new TextResponse($control->toString());

            $this->sendResponse($response);
        }
    }

    /**
     * Vygenerovani odkazu na vlozenou mapu
     */
    public function renderEmbeddedInfo()
    {
        if (!$this->isAjax()) {
            $this->forward('default', [self::PARAM_POPUP => self::COMPONENT_EMBEDDED_INFO]);
        } else {
            $keys = [MapControl::GET_CENTER_LAT, MapControl::GET_CENTER_LNG, MapControl::GET_ZOOM, MapControl::GET_MAPS];
            $mapParams = [];

            foreach ($keys as $key) {
                $mapParams[$key] = $this->request->getParameter($key);
            }

            /** @var EmbeddedInfoControl $control */
            $control = $this[self::COMPONENT_EMBEDDED_INFO];
            $control->setMapParams($mapParams);

            $response = new TextResponse($control->toString());

            $this->sendResponse($response);
        }
    }

    /**
     * Napoveda/dokumentace
     */
    public function renderHelp()
    {
        if (!$this->isAjax()) {
            $this->forward('default', [self::PARAM_POPUP => self::COMPONENT_HELP]);
        } else {
            /** @var HelpControl $control */
            $control = $this[self::COMPONENT_HELP];

            $response = new TextResponse($control->getTemplate());

            $this->sendResponse($response);
        }
    }

    /**
     * Routa pro prevod formatu od DPA do interniho CSV.
     */
    public function actionParseDpa()
    {
        $data = file_get_contents(STORAGE_DIR . "/example/dpa.csv");

        $data = $this->dpaTransformator->transform($data);

        $response = new TextResponse($data);

        $this->getHttpResponse()->setContentType("text/csv");
        $this->sendResponse($response);
    }

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

        return $control;
    }

    /**
     * @param IMapControlFactory $factory
     *
     * @return MapControl
     */
    protected function createComponentMap(IMapControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IFilterControlFactory $factory
     *
     * @return FilterControl
     */
    protected function createComponentFilter(IFilterControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param INavigationControlFactory $factory
     *
     * @return NavigationControl
     */
    protected function createComponentNavigation(INavigationControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IDetailControlFactory $factory
     *
     * @return DetailControl
     */
    protected function createComponentDetail(IDetailControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IExportControlFactory $factory
     *
     * @return ExportControl
     */
    protected function createComponentExport(IExportControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IEmbeddedInfoControlFactory $factory
     *
     * @return EmbeddedInfoControl
     */
    protected function createComponentEmbeddedInfo(IEmbeddedInfoControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param IHelpControlFactory $factory
     *
     * @return HelpControl
     */
    protected function createComponentHelp(IHelpControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * @param INewsControlFactory $factory
     *
     * @return NewsControl
     */
    protected function createComponentNews(INewsControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }
}
