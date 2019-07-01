<?php

namespace MP\Module\Web\Component\MarkersControl;

use MP\Component\AbstractControl;
use MP\Manager\ObjectManager;
use MP\Service\FilterService;
use MP\Util\Arrays;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\Json;

/**
 * Komponenta pro vykresleni markeru mapy
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class MarkersControl extends AbstractControl
{
    /** @const Typ markeru. */
    const MARKER_TYPE_GROUP = 'group';

    /** @const Cesta k obrazkum markeru */
    const PATH_TO_MARKER_IMAGES = '/asset/img/markers/%s.png';

    /**
     * @const
     * Pokud pocet groupovanych objektu presahne tento pocet, vypise se vychozi groupovaci marker
     * Muze vypadat napr. "3+"
     */
    const MAX_GRAPHIC_GROUP_COUNT = 4;

    /**
     * @persistent
     * @var bool
     */
    public $renderable = false;

    /** @var ObjectManager */
    protected $objectManager;

    /** @var Cache */
    protected $cache;

    /** @var array */
    private $categories;

    /** @var array */
    private $restrictor;

    /** @var array */
    private $object;

    /** @var string */
    private $accessibilityType;

    /**
     * @param ObjectManager $objectManager
     * @param IStorage $storage
     * @param array $categories
     */
    public function __construct(
        ObjectManager $objectManager,
        IStorage $storage,
        array $categories
    ) {
        $this->objectManager = $objectManager;
        $this->cache = new Cache($storage);
        $this->categories = Arrays::flip($categories);
    }

    public function render()
    {
        if ($this->isRenderable()) {
            $template = $this->getTemplate();
            $template->markers = $this->prepareMarkers();
            $template->object = $this->prepareObject();
            $template->render();
        }
    }

    /**
     * @return bool
     */
    public function isRenderable(): bool
    {
        return (bool) $this->renderable;
    }

    /**
     * @param bool $renderable
     */
    public function setRenderable(bool $renderable)
    {
        $this->renderable = $renderable;
    }

    /**
     * @param array|null $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @param array $restrictor
     */
    public function setRestrictor(array $restrictor)
    {
        $this->restrictor = $restrictor;
    }


    public function setAccessibilityType(string $accessibilityType)
    {
        $this->accessibilityType = $accessibilityType;
    }

    /**
     * Seskupeni objektu na stejne adrese a priprava dat pro vykresleni markeru.
     *
     * @return string
     */
    private function prepareMarkers(): string
    {
        return $this->cache->load($this->restrictor, function(&$dependencies) {
            return $this->innerPrepareMarkers($this->restrictor);
        });
    }

    /**
     * @return string
     */
    private function prepareObject(): string
    {
        return Json::encode($this->object);
    }

    /**
     * Pripravi informaci o tom, jaky se ma pouzit marker, celkem existuji markery pro:
     *  kategorii objektu (cca 21)
     *  pristupnost (3)
     *  typ podkladu (2)
     * celkem tedy cca 126 typu ikonky
     *
     * @param array $object
     *
     * @return array
     */
    private function getMarkerType($object)
    {
        // zastarale profesionalni udaje se markerem nelisi od aktualnich
        $isCommunityMarker = $object['type'] === FilterService::TYPE_COMMUNITY;
        $aggregated_accessibility_id = $object['accessibility_id'];

        if ($this->accessibilityType === FilterService::ACCESSIBILITY_TYPE_PRAM && $object['accessibility_pram_id']) {
            $aggregated_accessibility_id = $object['accessibility_pram_id'];
        } else if ($this->accessibilityType === FilterService::ACCESSIBILITY_TYPE_SENIORS && $object['accessibility_seniors_id']) {
            $aggregated_accessibility_id = $object['accessibility_seniors_id'];
        }

        return [$isCommunityMarker, $aggregated_accessibility_id, $this->categories[$object['object_type_id']] ?? 'other'];
    }

    /**
     * Objekty seskupuji pokud je jejich vzajemna vzdalenost mensi nez self::MARKER_GROUP_MAX_DISTANCE.
     *
     * @param array $objects
     *
     * @return array
     */
    private function groupObjects($objects)
    {
        $groups = [];

        foreach ($objects as $object) {
            $groups[$object['cluster_id']][$object['object_id']] = $object;
        }

        return $groups;
    }

    /**
     * @param array $restrictor
     *
     * @return string
     */
    private function innerPrepareMarkers($restrictor)
    {
        $objects = $this->objectManager->findMarkers($restrictor);

        $markers = [];

        // 1. seskupeni dle stejne adresy
        $groups = $this->groupObjects($objects);

        // 2. priprava markeru
        foreach ($groups as $group) {
            $count = count($group);
            $first = reset($group);
            $objectIds = array_keys($group);

            // group
            if ($count > 1) {
                $type = self::MARKER_TYPE_GROUP;
                $markerPath = 'cluster/marker_cluster_' . ($count >= static::MAX_GRAPHIC_GROUP_COUNT ? static::MAX_GRAPHIC_GROUP_COUNT : $count);
                // others
            } else {
                $type = $this->getMarkerType($first);

                // suffix
                if (true === $type[0]) {
                    $markerSuffix = '_c';
                } else {
                    $markerSuffix = null;
                }

                // napr. "1/churches_c"
                // @see spec/markery.ods (puvodni nazvy obrazku)
                $markerPath = "{$type[1]}/{$type[2]}{$markerSuffix}";
            }

            $markers[] = [
                'id' => implode(';', $objectIds),
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
}
