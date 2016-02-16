<?php

namespace MP\Module\Web\Component\MapControl;

use MP\Component\AbstractControl;
use MP\Manager\ObjectManager;
use MP\Module\Web\Component\InfoBoxControl\IInfoBoxControlFactory;
use MP\Module\Web\Component\InfoBoxControl\InfoBoxControl;
use MP\Service\FilterService;
use MP\Util\Arrays;
use Nette\Application\Responses\TextResponse;
use Nette\Http\IRequest;
use Nette\Utils\Json;

/**
 * Komponenta pro vykresleni mapy.
 */
class MapControl extends AbstractControl
{
    const MARKER_GROUP_MAX_DISTANCE = 0.0002;

    /** @const GET parametry pro konfiguraci mapy. */
    const GET_CENTER_LAT = 'center-lat',
        GET_CENTER_LNG = 'center-lng',
        GET_ZOOM = 'zoom';

    /** @const Nazev komponent filtru objektu. */
    const COMPONENT_FILTER = 'filter';
    /** @const Nazev komponenty pro info box. */
    const COMPONENT_INFO_BOX = 'infoBox';

    /** @const Typ markeru. */
    const MARKER_TYPE_GROUP = 'group';

    /** @const Cesta k obrazkum markeru */
    const PATH_TO_MARKER_IMAGES = "/asset/img/markers/%s.png";

    /**
     * @const
     * Pokud pocet groupovanych objektu presahne tento pocet, vypise se vychozi groupovaci marker
     * Muze vypadat napr. "3+"
     */
    const MAX_GRAPHIC_GROUP_COUNT = 4;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var IInfoBoxControlFactory */
    protected $infoBoxFactory;

    /** @var IRequest */
    protected $request;

    /** @var array */
    protected $categories;

    /** @var array */
    protected $restrictor;

    /** @var bool vykreslovano jako embedded mapa? */
    protected $embedded = false;

    /**
     * @param ObjectManager $objectManager
     * @param IInfoBoxControlFactory $infoBoxFactory
     * @param IRequest $request
     * @param array $categories
     */
    public function __construct(
        ObjectManager $objectManager,
        IInfoBoxControlFactory $infoBoxFactory,
        IRequest $request,
        array $categories
    )
    {
        $this->objectManager = $objectManager;
        $this->infoBoxFactory = $infoBoxFactory;
        $this->request = $request;
        $this->categories = Arrays::flip($categories);
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->config = $this->prepareConfig();
        $template->render();
    }

    public function renderMarkers()
    {
        $template = $this->getTemplate('.markers');
        $template->markers = $this->prepareMarkers();
        $template->render();
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
     * @param array $restrictor
     */
    public function setRestrictor($restrictor)
    {
        $this->restrictor = $restrictor;
    }

    /**
     * Seskupeni objektu na stejne adrese a priprava dat pro vykresleni markeru.
     *
     * @return string
     */
    protected function prepareMarkers()
    {
        $objects = $this->objectManager->findMarkers($this->restrictor);

        $markers = [];

        // 1. seskupeni dle stejne adresy
        $groups = $this->groupObjects($objects);

        // 2. priprava markeru
        foreach ($groups as $group_key => $group) {
            $count = count($group);
            $first = reset($group);
            $objectIds = array_keys($group);

            // group
            if ($count > 1) {
                $type = self::MARKER_TYPE_GROUP;
                $markerPath = "cluster/marker_cluster_" . ($count >= static::MAX_GRAPHIC_GROUP_COUNT ? static::MAX_GRAPHIC_GROUP_COUNT : $count);
            // others
            } else {
                $type = $this->getMarkerType($first);

                // suffix
                if (FilterService::TYPE_COMMUNITY === $type[0]) {
                    $markerSuffix = "_c";
                } else {
                    $markerSuffix = null;
                }

                // napr. "1/churches_c"
                // @see spec/markery.ods (puvodni nazvy obrazku)
                $markerPath = "{$type[1]}/{$type[2]}{$markerSuffix}";
            }

            $markers[] = [
                'id' => md5(implode(';', $objectIds)),
                'title' => $first['title'],
                'count' => $count,
                'type' => $type,
                'latitude' => $first['latitude'],
                'longitude' => $first['longitude'],
                'object_ids' => $objectIds,
                'image' => sprintf(static::PATH_TO_MARKER_IMAGES, $markerPath),
            ];
        }

        return Json::encode($markers);
    }

    /**
     * Pripravi informaci o tom, jaky se ma pouzit marker, celkem existuji markery pro:
     *  kategorii objektu (cca 21)
     *  pristupnost (3)
     *  typ podkladu (2)
     * celkem tedy cca 126 typu ikonky
     *
     * @param $object
     *
     * @return array
     */
    protected function getMarkerType($object)
    {
        // zastarale profesionalni udaje se markerem nelisi od aktualnich
        $certified_prefix = $object['type'] == (FilterService::TYPE_COMMUNITY ? FilterService::TYPE_COMMUNITY : FilterService::TYPE_CERTIFIED);

        return [$certified_prefix, $object['accessibility_id'], Arrays::get($this->categories, $object['object_type_id'], 'other')];
    }

    /**
     * Pripravi konfiguraci mapy.
     *
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    protected function prepareConfig()
    {
        $config = [
            'center' => [
                'lat' => (float) $this->request->getQuery(self::GET_CENTER_LAT, 49.5),
                'lng' => (float) $this->request->getQuery(self::GET_CENTER_LNG, 14.9),
            ],
            'zoom' => (int) $this->request->getQuery(self::GET_ZOOM, 8),
            'streetViewControl' => false,
            'zoomControl' => false,
            'mapTypeControl' => false,
        ];

        return Json::encode($config);
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
     * @param bool $embedded
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = $embedded;
    }

    /**
     * Objekty seskupuji pokud maji stejne RUIAN ID a rozdil jejich GPS souradnic je pod 0.0002
     * @param array $objects
     *
     * @return array
     */
    protected function groupObjects($objects)
    {
        $ruianAddressGroups = $groups = [];

        foreach ($objects as $item) {
            if ($item['ruian_address']) {
                $groupFound = false;

                if (!empty($ruianAddressGroups[$item['ruian_address']])) {
                    foreach ($ruianAddressGroups[$item['ruian_address']] as $ruian_group) {
                        $putTogether = true;

                        foreach ($groups[$ruian_group] as $previousItem) {
                            if (
                                (abs($item['latitude'] - $previousItem['latitude']) > self::MARKER_GROUP_MAX_DISTANCE)
                                || (abs($item['longitude'] - $previousItem['longitude']) > self::MARKER_GROUP_MAX_DISTANCE)
                            ) {
                                $putTogether = false;
                                break;
                            }
                        }

                        if ($putTogether) {
                            $groups[$ruian_group][$item['object_id']] = $item;
                            $groupFound = true;
                            break;
                        }
                    }
                }

                if (!$groupFound) {
                    $ruian_group = "{$item['ruian_address']}-parent-{$item['object_id']}";
                    $ruianAddressGroups[$item['ruian_address']][] = $ruian_group;
                    $groups[$ruian_group][$item['object_id']] = $item;
                }
            } else {
                $groups["id-{$item['object_id']}"][$item['object_id']] = $item;
            }
        }

        return $groups;
    }
}
