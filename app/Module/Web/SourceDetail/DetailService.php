<?php

namespace MP\Module\SourceDetail;

use Dibi\DateTime;
use Kdyby\Translation\Translator;
use MP\Manager\ImageManager;
use MP\Module\Web\Service\ObjectService;
use MP\Object\ObjectHelper;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Http\Url;
use Nette\InvalidArgumentException;

/**
 * Sluzba pro pripravu dat o mapovych objektech pro detail
 */
class DetailService
{
    const MKPO_MIN_DOOR_WIDTH = 80;
    const RAMP_MAX_LENGTH_1 = 300;
    const RAMP_MAX_INCLINATION_1 = 12.5;
    const RAMP_MAX_LENGTH_2 = 900;
    const RAMP_MAX_INCLINATION_2 = 8.0;

    /** @var ObjectService */
    protected $objectService;

    /** @var SourceDetailFactory */
    protected $sourceDetailFactory;

    /** @var Translator */
    protected $translator;

    /** @var array */
    protected $categories;

    /** @var ImageManager */
    protected $imageManager;

    /**
     * @param ObjectService $objectService
     * @param SourceDetailFactory $sourceDetailFactory
     * @param Translator $translator
     * @param ImageManager $imageManager
     * @param array $categories
     */
    public function __construct(ObjectService $objectService, SourceDetailFactory $sourceDetailFactory, Translator $translator, ImageManager $imageManager, array $categories)
    {
        $this->objectService = $objectService;
        $this->sourceDetailFactory = $sourceDetailFactory;
        $this->translator = $translator;
        $this->imageManager = $imageManager;
        $this->categories = Arrays::flip($categories);
    }

    /**
     * @param array $object
     *
     * @return array
     */
    public function getDetail(array $object)
    {
        return $this->prepareDetailData($object);
    }

    /**
     * Vrati data pro vypis obou urovni detailu objektu
     *
     * @param int $id
     *
     * @return array
     */
    public function getDetailById($id)
    {
        $object = $this->objectService->getObjectByObjectId($id);

        return $this->prepareDetailData($object);
    }

    /**
     * Sestavi informace pro vykresleni detailu (pro obe urovne)
     *
     * @param array $object
     *
     * @return array
     */
    protected function prepareDetailData($object)
    {
        $outdatedLimit = $object['mapping_date']->modifyClone('+ 3 years');
        $sourceDetail = $this->sourceDetailFactory->create($object);

        try {
            $url = new Url($object['web_url']);

            if (!$url->getScheme()) {
                $url->setScheme('http');
            }

            $stringUrl = trim($url->getAuthority() . $url->getBasePath() . $url->getRelativeUrl(), '/');
            $linkUrl = $url->getAbsoluteUrl();
        } catch (InvalidArgumentException $e) {
            $linkUrl = '';
            $stringUrl = '';
        }

        $ret = [
            'id' => $object['object_id'],
            'title' => $object['title'],
            'image' => $this->imageManager->find($object['object_id'], ImageManager::NAMESPACE_OBJECT),
            'category' => $this->getCategory($object),
            'outdated' => empty($object['mapping_date']) || ($outdatedLimit <= new DateTime()),
            'address' => ObjectHelper::getAddressString($object),
            'string_url' => $stringUrl,
            'link_url' => $linkUrl,
            'longitude' => $object['longitude'],
            'latitude' => $object['latitude'],
            'source' => $object['source'],
            'owner' => $object['data_owner_url'],
            'accessibility' => [
                'id' => $object['accessibility_id'],
                'title' => $object['accessibility'],
            ],
            'wc_accessibility' => $this->getWcAccessibility($object),
            'wc_is_changing_desk' => $this->getWcIsChangingDesk($object),
            'entrance1_accessibility' => $this->getEntranceAccessibility($object, 'entrance1', ObjectMetadata::ENTRANCE_TYPE_1),
            'entrance2_accessibility' => $this->getEntranceAccessibility($object, 'entrance2', ObjectMetadata::ENTRANCE_TYPE_2),
            'descriptions' => $this->getDescriptions($object),
            'pictograms' => $this->getPictogramsData($object),
            'source_id' => $object['source_id'],
            'custom_data' => ($sourceDetail ? $sourceDetail->prepareSourceData($object) : null),
        ];

        return $ret;
    }

    /**
     * Pristupnost WC - nejprisutpnejsi kategorie u wc v celem objektu
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getWcAccessibility($object)
    {
        $ret = null;

        foreach (Arrays::get($object, ObjectMetadata::WC, []) as $wc) {
            if ($wc['wc_accessibility_id'] && (!$ret || ($ret['id'] > $wc['wc_accessibility_id']))) {
                $ret = [
                    'id' => $wc['wc_accessibility_id'],
                    'title' => $wc['wc_accessibility'],
                ];
            }
        }

        return $ret;
    }

    /**
     * Je alespon na jednom WC prebalovaci pult?
     *
     * @param array $object
     *
     * @return bool
     */
    protected function getWcIsChangingDesk($object)
    {
        $ret = false;

        foreach (Arrays::get($object, ObjectMetadata::WC, []) as $wc) {
            if ($wc['wc_is_changingdesk']) {
                $ret = true;
                break;
            }
        }

        return $ret;
    }

    /**
     * Je vstup pristupny? (bez prevyseni nebo s prisutpnou rampou)
     *
     * @param array $object
     * @param string $entrancePrefix
     * @param string $rampRelation
     *
     * @return bool
     */
    protected function getEntranceAccessibility($object, $entrancePrefix, $rampRelation)
    {
        $ret = (
            (ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION === $object["{$entrancePrefix}_accessibility"])
            || (
                (ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP === $object["{$entrancePrefix}_accessibility"])
                && $this->existsAccessibleRamp($object, $rampRelation)
            )
        );

        return $ret;
    }

    /**
     * Urceni, ktere piktogramy se maji zobrazit a jejich textoveho upresneni
     * vsechny piktogramy vraci tvar: [
     *     'value' => string|boolean
     *     'description' => string|null // nepovinne
     *     'accessible' => boolean // true - zelene, false - cervene, null - bez zvyrazneni
     * ], klic pak urcuje ikonku a hlavni text
     *
     * @param array $object
     *
     * @return array
     */
    protected function getPictogramsData($object)
    {
        $ret =
            $this->getWcPictograms($object) +
            [
                'parking' => $this->getPictogramParking($object),
                'difficult_surface' => $this->getPictogramDifficultSurface($object),
                'difficult_inclination' => $this->getPictogramDifficultInclination($object),
                'stairs' => $this->getPictogramStairs($object),
                'spiral_stairs' => $this->getPictogramSpiralStairs($object),
                'elevator' => $this->getPictogramElevator($object),
                'platform' => $this->getPictogramPlatform($object),
                'rampskids' => $this->getPictogramRampSkids($object),
                'narrowed_passage' => $this->getPictogramNarrowedPassage($object),
                'door_width' => $this->getPictogramDoorWidth($object),
            ];

        return $ret;
    }

    /**
     * Ma vyhrazena parkovaci stani? Pokud ano, tak jejich pocet
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramParking($object)
    {
        $ret = [
            'value' => false,
            'accessible' => false,
        ];

        if ($object['entrance1_is_reserved_parking'] || $object['entrance2_is_reserved_parking']) {
            $ret['accessible'] = true;
            $ret['value'] = $object['entrance1_number_of_reserved_parking'] + $object['entrance2_number_of_reserved_parking'];
        }

        return $ret;
    }

    /**
     * Ma schody? Pokud ano, tak jejich nejvetsi pocet
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramStairs($object)
    {
        $ret = [
            'value' => false,
            'accessible' => true,
        ];

        $countKeys = [
            'entrance1_steps1_number_of', 'entrance1_steps2_number_of',
            'entrance2_steps1_number_of', 'entrance2_steps2_number_of',
        ];

        $heightKeys = [
            'entrance1_steps1_height', 'entrance1_steps2_height',
            'entrance2_steps1_height', 'entrance2_steps2_height',
        ];

        foreach (array_merge($countKeys, $heightKeys) as $key) {
            if ($object[$key] > 0) {
                $ret['value'] = true;
                $ret['accessible'] = null;
                break;
            }
        }

        if (!$ret['value'] && ($object['object_is_steps'] || $object['object_is_stairs'])) {
            $ret['value'] = true;
            $ret['accessible'] = null;
        }

        if ($ret['value']) {
            $ret['value'] = 1;
            $ret['accessible'] = null;

            foreach ($countKeys as $key) {
                if ($object[$key] > $ret['value']) {
                    $ret['value'] = $object[$key];
                }
            }
        }

        return $ret;
    }

    /**
     * Ma tocite schodiste? (bez blizsiho popisku)
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramSpiralStairs($object)
    {
        $ret = [
            'value' => false,
            'accessible' => true,
        ];

        if (
            ObjectMetadata::STAIRS_SPIRAL_TYPE === $object['object_stairs_type'] 
            || ObjectMetadata::STAIRS_DIRECT_SPIRAL_TYPE === $object['object_stairs_type']
        ) {
            $ret = [
                'value' => true,
                'accessible' => false,
            ];
        }

        return $ret;
    }

    /**
     * Ma vytah? Pokud ano, tak rozmery toho nejmensiho
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramElevator($object)
    {
        $ret = [
            'value' => false,
            'accessible' => null,
        ];

        if ($object['elevator']) {
            $ret['value'] = true;

            $minArea = null;

            foreach ($object['elevator'] as $elevator) {
                $area = $elevator['elevator_cage_width'] * $elevator['elevator_cage_depth'];

                if ($area && (is_null($minArea) || $minArea > $area)) {
                    $ret['description'] = $this->translator->translate('messages.control.map.detail.description.elevator', [
                        'size' => "{$elevator['elevator_cage_width']} × {$elevator['elevator_cage_depth']}",
                    ]);
                }
            }
        }

        return $ret;
    }

    /**
     * Ma plosinu? Pokud ano, tak rozmery te nejmensi
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramPlatform($object)
    {
        $ret = [
            'value' => false,
            'accessible' => null,
        ];

        if ($object['platform']) {
            $ret['value'] = true;

            $minArea = null;

            foreach ($object['platform'] as $platform) {
                $area = $platform['platform_width'] * $platform['platform_depth'];

                if ($area && (is_null($minArea) || $minArea > $area)) {
                    $ret['description'] = $this->translator->translate('messages.control.map.detail.description.platform', [
                        'size' => "{$platform['platform_width']} × {$platform['platform_depth']}",
                    ]);
                }
            }
        }

        return $ret;
    }

    /**
     * Ma rampu/liziny? Pokud ano, tak nejvetsi sklon/delku
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramRampSkids($object)
    {
        $ret = [
            'value' => false,
            'accessible' => null,
        ];

        if ($object['rampskids']) {
            $ret['value'] = true;

            $maxRampSkidsValues = [];

            // nejprve pro kazdou rampu/liziny vyberu rameno/liziny s nejvetsim sklonem a delkou
            foreach ($object['rampskids'] as $rampskids) {
                list($maxLenght, $maxInclination) = $this->getMaxRampSkidsValues($rampskids);

                $maxRampSkidsValues[$rampskids['id']] = [
                    'inclination' => $maxInclination,
                    'length' => $maxLenght,
                ];
            }

            // pokud je nejvetsi sklon i delka u stejne rampy/lizin, uvadim pouze sklon, jinak oboje
            $totalMaxInclination = $totalMaxLenght = 0;
            $idInclination = $idLength = null;

            foreach ($maxRampSkidsValues as $id => $maxValues) {
                if ($maxValues['inclination'] > $totalMaxInclination) {
                    $totalMaxInclination = $maxValues['inclination'];
                    $idInclination = $id;
                }

                if ($maxValues['length'] > $totalMaxLenght) {
                    $totalMaxLenght = $maxValues['length'];
                    $idLength = $id;
                }
            }

            if ($totalMaxInclination) {
                $ret['description'] = $this->translator->translate('messages.control.map.detail.description.rampskids1', [
                    'size' => $totalMaxInclination,
                ]);
            }

            if ($totalMaxInclination && $totalMaxLenght && $idInclination != $idLength) {
                $ret['description'] .= ', ' . $this->translator->translate('messages.control.map.detail.description.rampskids2', [
                        'size' => $totalMaxLenght,
                    ]);
            }
        }

        return $ret;
    }

    /**
     * Ma zuzeny pruchod? Pokud ano, tak jeho sirka
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramNarrowedPassage($object)
    {
        $ret = [
            'value' => false,
            'accessible' => true,
        ];

        if ($object['object_is_narrowed_passage']) {
            $ret = [
                'value' => $object['object_narrowed_passage_width'] . ' cm',
                'accessible' => false,
            ];
        }

        return $ret;
    }

    /**
     * Ma zuzene dvere? Pokud ano, tak minimalni sirka
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramDoorWidth($object)
    {
        $ret = [
            'value' => false,
            'accessible' => true,
        ];

        $minWidth = null;

        $entranceKeys = [
            ['entrance1_door1_mainpanel_width', 'entrance1_door1_sidepanel_width'],
            ['entrance1_door2_mainpanel_width', 'entrance1_door2_sidepanel_width'],
            ['entrance2_door1_mainpanel_width', 'entrance2_door1_sidepanel_width'],
            ['entrance2_door1_mainpanel_width', 'entrance2_door2_sidepanel_width'],
        ];

        foreach ($entranceKeys as $key) {
            $width = $object[$key[0]] + $object[$key[1]];

            if ($width && (!$minWidth || $width < $minWidth)) {
                $minWidth = $width;
            }
        }

        $elevatorKeys = [
            'door1_width', 'door2_width',
        ];

        foreach ($object['elevator'] as $elevator) {
            foreach ($elevatorKeys as $key) {
                if ($elevator[$key] && (!$minWidth || $elevator[$key] < $minWidth)) {
                    $minWidth = $elevator[$key];
                }
            }
        }

        $wcKeys = [
            'hallway1_door_width', 'hallway1_door_width', 'door_width',
        ];

        foreach ($object['wc'] as $wc) {
            foreach ($wcKeys as $key) {
                if ($wc[$key] && (!$minWidth || $wc[$key] < $minWidth)) {
                    $minWidth = $wc[$key];
                }
            }
        }

        if ($minWidth && $minWidth < self::MKPO_MIN_DOOR_WIDTH) {
            $ret = [
                'value' => $minWidth . ' cm',
                'accessible' => false,
            ];
        }

        return $ret;
    }

    /**
     * Pokusi se najit rampu pro dany vchod a vyhodnoti, zda je pristupna
     *
     * @param array $object
     * @param string $rampRelation
     *
     * @return bool
     */
    protected function existsAccessibleRamp($object, $rampRelation)
    {
        $ret = false;

        foreach ($object['rampskids'] as $rampskids) {
            if ($rampRelation === $rampskids['ramp_relation']) {
                list($maxLenght, $maxInclination) = $this->getMaxRampSkidsValues($rampskids);

                if ($maxLenght <= self::RAMP_MAX_LENGTH_1) {
                    $ret = ($maxInclination <= self::RAMP_MAX_INCLINATION_1);
                } else if ($maxLenght <= self::RAMP_MAX_LENGTH_2) {
                    $ret = ($maxInclination <= self::RAMP_MAX_INCLINATION_2);
                }

                break;
            }
        }

        return $ret;
    }

    /**
     * Vypocet max. delky a sklonu ramena/lizin
     *
     * @param array $rampskids
     *
     * @return array 2prvkove pole delka, sklon
     */
    protected function getMaxRampSkidsValues($rampskids)
    {
        $maxInclination = $maxLenght = 0;

        for ($i = 1; $i <= 4; $i++) {
            if ($rampskids["rampleg{$i}_inclination"] > $maxInclination) {
                $maxInclination = $rampskids["rampleg{$i}_inclination"];
            }

            if ($rampskids["rampleg{$i}_length"] > $maxLenght) {
                $maxLenght = $rampskids["rampleg{$i}_length"];
            }
        }

        if ($rampskids['skids_inclination'] > $maxInclination) {
            $maxInclination = $rampskids['skids_inclination'];
        }

        if ($rampskids['skids_length'] > $maxLenght) {
            $maxLenght = $rampskids['skids_length'];
        }

        return [$maxLenght, $maxInclination];
    }

    /**
     * Ma nerovny povrch? U ktereho vchodu?
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramDifficultSurface($object)
    {
        $ret = [
            'value' => false,
            'accessible' => true,
        ];

        if ($object['entrance1_is_difficult_surface'] || $object['entrance2_is_difficult_surface']) {
            $description = [];

            if ($object['entrance1_is_difficult_surface']) {
                $description[] = $this->translator->translate('messages.control.map.detail.description.entrance1');
            }

            if ($object['entrance2_is_difficult_surface']) {
                $description[] = $this->translator->translate('messages.control.map.detail.description.entrance2');
            }

            $ret = [
                'value' => true,
                'accessible' => false,
                'description' => implode(', ', $description),
            ];
        }

        return $ret;
    }

    /**
     * Ma obtizny sklon? U ktereho vchodu?
     *
     * @param array $object
     *
     * @return null|array
     */
    protected function getPictogramDifficultInclination($object)
    {
        $ret = [
            'value' => false,
            'accessible' => true,
        ];

        $entrance1 = $object['entrance1_is_longitudinal_inclination'] || $object['entrance1_is_transverse_inclination'];
        $entrance2 = $object['entrance2_is_longitudinal_inclination'] || $object['entrance2_is_transverse_inclination'];

        if ($entrance1 || $entrance2) {
            $description = [];

            if ($entrance1) {
                $description[] = $this->translator->translate('messages.control.map.detail.description.entrance1');
            }

            if ($entrance2) {
                $description[] = $this->translator->translate('messages.control.map.detail.description.entrance2');
            }
            $ret = [
                'value' => true,
                'accessible' => false,
                'description' => implode(', ', $description),
            ];
        }

        return $ret;
    }

    /**
     * Pripravi popisne texty objektu a jeho casti.
     *
     * @param array $object
     *
     * @return array
     */
    protected function getDescriptions($object)
    {
        $desriptions = [
            'object' => $object['description'],
            'mainEntrance' => $object['entrance1_has_description'],
            'sideEntrance' => $object['entrance2_has_description'],
            'interior' => $object['object_has_description'],
            'rampskids' => [],
            'platform' => [],
            'elevator' => [],
            'wc' => [],
        ];

        $mapping = [
            ObjectMetadata::RAMP_SKIDS => 'ramp_skids_has_description',
            ObjectMetadata::PLATFORM => 'platform_has_description',
            ObjectMetadata::ELEVATOR => 'elevator_has_description',
            ObjectMetadata::WC => 'wc_has_description',
        ];

        foreach ($mapping as $key => $description) {
            foreach ($object[$key] as $values) {
                if ($description = Arrays::get($values, $description, null)) {
                    $desriptions[$key][] = $description;
                }
            }
        }

        return $desriptions;
    }

    /**
     * Pripravi kategorii objektu.
     *
     * Pokud se jedna o objekt z kategorie jine, pak nastavuje jako title custom type, pokud tento neni prazdny.
     *
     * @param array $object
     *
     * @return array
     */
    protected function getCategory($object)
    {
        if (ObjectMetadata::CATEGORY_OTHER === $object['object_type']) {
            if ($object['object_type_custom']) {
                $title = $object['object_type_custom'];
            } else {
                $title = $this->translator->translate('messages.enum.value.category.otherObjectCategory');
            }
        } else {
            $title = $this->translator->translate('messages.enum.value.category.' . Strings::firstLower($object['object_type']));
        }

        $category = [
            'id' => Arrays::get($this->categories, $object['object_type_id'], 'other'),
            'title' => $title,
        ];

        return $category;
    }

    /**
     * Pripravi piktogramy WC pro vsechny prilohy
     *
     * @param array $object
     *
     * @return array
     */
    protected function getWcPictograms($object)
    {
        $ret = [];

        foreach (Arrays::get($object, ObjectMetadata::WC, []) as $wc) {
            if ($wc['wc_accessibility']) {
                $ret['wc_' . $wc['id']] = [
                    'key' => $wc['wc_accessibility'],
                    'value' => '',
                    'accessible' => null,
                    'description' => $this->getWcDescription($wc),
                ];
            }
        }

        return $ret;
    }

    /**
     * Sestavi detailni info o konkretnim WC
     *
     * @param array $wc
     *
     * @return string
     */
    protected function getWcDescription($wc)
    {
        $parts = [];

        if ($wc['wc_cabin_localization_id'] && $wc['wc_cabin_access_id']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.location', [
                'localization' => $this->translator->translate('messages.enum.value.wcCabinLocalization.' . $wc['wc_cabin_localization']),
                'access' => $this->translator->translate('messages.enum.value.wcCabinAccess.' . $wc['wc_cabin_access']),
            ]);
        }

        if ($wc['wc_cabin_width'] && $wc['wc_cabin_depth']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.cabinSize', [
                'size' => "{$wc['wc_cabin_width']} × {$wc['wc_cabin_depth']}",
            ]);
        }

        if ($wc['door_opening_direction_id']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.doorOpeningDirection', [
                'value' => $this->translator->translate('messages.enum.value.doorOpeningDirection.' . $wc['door_opening_direction']),
            ]);
        }

        if ($wc['wc_basin_left_distance']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.wcBasinLeftDistance', [
                'size' => $wc['wc_basin_left_distance'],
            ]);
        }

        if ($wc['wc_basin_right_distance']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.wcBasinRightDistance', [
                'size' => $wc['wc_basin_right_distance'],
            ]);
        }

        if ($wc['wc_basin_seat_height']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.wcBasinSeatHeight', [
                'size' => $wc['wc_basin_seat_height'],
            ]);
        }

        if ($wc['handle1_type_id'] && $wc['handle1_length']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.handleLeft', [
                'size' => $wc['handle1_length'],
                'type' => $this->translator->translate('messages.enum.value.handleType.' . $wc['handle1_type']),
            ]);
        } else {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.handleLeftMissing');
        }

        if ($wc['handle2_type_id'] && $wc['handle2_length']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.handleRight', [
                'size' => $wc['handle2_length'],
                'type' => $this->translator->translate('messages.enum.value.handleType.' . $wc['handle2_type']),
            ]);
        } else {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.handleRightMissing');
        }

        if ($wc['washbasin_underpass_id']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.washbasinUnderpass', [
                'value' => $this->translator->translate('messages.enum.value.washbasinUnderpass.' . $wc['washbasin_underpass']),
            ]);
        }

        if ($wc['wc_changingdesk_id']) {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.wcChangingdeskTrue', [
                'bool' => $this->translator->translate('messages.enum.boolLower.' . ($wc['wc_is_changingdesk'] ? "true" : "false")),
                'value' => $this->translator->translate('messages.enum.value.wcChangingdesk.' . $wc['wc_changingdesk']),
            ]);
        } else {
            $parts[] = $this->translator->translate('messages.control.map.detail.description.wc.wcChangingdeskFalse', [
                'bool' => $this->translator->translate('messages.enum.boolLower.' . ($wc['wc_is_changingdesk'] ? "true" : "false")),
            ]);
        }

        return implode("\n", $parts);
    }
}
