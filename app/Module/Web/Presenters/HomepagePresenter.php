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
use MP\Module\Web\Component\MapControl\MapControl;
use MP\Module\Web\Component\MarkersControl\MarkersControl;
use MP\Module\Web\Component\NavigationControl\INavigationControlFactory;
use MP\Module\Web\Component\NavigationControl\NavigationControl;
use MP\Module\Web\Component\NewsControl;
use MP\Module\Web\Service\ObjectRestrictorBuilder;
use MP\Module\Web\Service\ObjectService;
use Nette\Application\Responses\TextResponse;
use Nette\Utils\FileSystem;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class HomepagePresenter extends AbstractWebPresenter
{
    use TMapPresenter;

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
    /** @const Nazev komponenty s markery mapy */
    const COMPONENT_MARKERS = 'markers';
    /** @const Nazev komponenty s novinkami */
    const COMPONENT_NEWS = 'news';

    /** @const Nazev parametru s jmenem popupu */
    const PARAM_POPUP = 'popup';
    /** @const Nazev parametru s volbou mapove sady */
    const PARAM_MAPS = 'maps';

    /** @var ObjectService @inject */
    public $objectService;

    /** @var ObjectManager @inject */
    public $objectManager;

    /** @var ObjectRestrictorBuilder @inject */
    public $objectRestrictorBuilder;

    /** @var DpaTransformator @inject */
    public $dpaTransformator;

    /** @var array */
    private $restrictor;

    /**
     * @param int|null $id
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionDefault(int $id = null)
    {
        if (null !== $id) {
            $object = $this->objectService->getObjectByObjectId($id);

            if (!$object) {
                throw new \Nette\Application\BadRequestException("Unknown object with ID '{$id}'.");
            }

            $this[self::COMPONENT_MARKERS]->setObject($object);
            $this[self::COMPONENT_DETAIL]->setObject($object);
            $this[self::COMPONENT_NEWS]->setRenderable(false);
        }

        $this->restrictor = $this->objectRestrictorBuilder->getRestrictor();
    }

    public function renderDefault()
    {
        $this[self::COMPONENT_MARKERS]->setRestrictor($this->restrictor);

        $this->template->filtered = (bool) $this->restrictor;
        $this->template->popup = $this->getParameter(self::PARAM_POPUP, null);
        $this->template->maps = $this->getParameter(self::PARAM_MAPS, null);
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
        $data = FileSystem::read(STORAGE_DIR . '/example/dpa.csv');

        $data = $this->dpaTransformator->transform($data);

        $response = new TextResponse($data);

        $this->getHttpResponse()->setContentType('text/csv');
        $this->sendResponse($response);
    }

    /**
     * @param IFilterControlFactory $factory
     *
     * @return FilterControl
     */
    protected function createComponentFilter(IFilterControlFactory $factory)
    {
        $control = $factory->create();
        $control->onFilterChanged[] = function (array $restrictor = null) {
            $this->restrictor = $restrictor;

            /** @var MarkersControl $markers */
            $markers = $this[self::COMPONENT_MARKERS];
            $markers->setRenderable(true);
            $markers->setRestrictor($restrictor);
            $markers->setAccessibilityType($this->objectRestrictorBuilder->getAccessibilityType());

            $this->redrawControl('filter');
            $this->redrawControl('markers');
        };

        return $control;
    }

    /**
     * @param INavigationControlFactory $factory
     *
     * @return NavigationControl
     */
    protected function createComponentNavigation(INavigationControlFactory $factory)
    {
        return $factory->create();
    }

    /**
     * @param IDetailControlFactory $factory
     *
     * @return DetailControl
     */
    protected function createComponentDetail(IDetailControlFactory $factory)
    {
        return $factory->create();
    }

    /**
     * @param IExportControlFactory $factory
     *
     * @return ExportControl
     */
    protected function createComponentExport(IExportControlFactory $factory)
    {
        return $factory->create();
    }

    /**
     * @param IEmbeddedInfoControlFactory $factory
     *
     * @return EmbeddedInfoControl
     */
    protected function createComponentEmbeddedInfo(IEmbeddedInfoControlFactory $factory)
    {
        return $factory->create();
    }

    /**
     * @param IHelpControlFactory $factory
     *
     * @return HelpControl
     */
    protected function createComponentHelp(IHelpControlFactory $factory)
    {
        return $factory->create();
    }

    /**
     * @param INewsControlFactory $factory
     *
     * @return NewsControl
     */
    protected function createComponentNews(INewsControlFactory $factory)
    {
        return $factory->create();
    }
}
