<?php

namespace MP\Object;

/**
 * Metada o entite objektu
 * Vyuziva se pro primy pristtup ke konkretnim atributum/hodnotam z kodu
 */
class ObjectMetadata
{
    /** @const Nazev tabulky objektu */
    const TABLE = 'map_object';

    /**
     * Hodnoty vybranych ciselniku
     */
    const CATEGORY_OTHER = 'OtherObjectCategory';

    const ACCESSIBILITY_OK = 'AccessibleObjectMKPO';
    const ACCESSIBILITY_PARTLY = 'PartlyAccessibleObjectMKPO';
    const ACCESSIBILITY_NO = 'InAccessibleObjectMKPO';

    const STAIRS_DIRECT_TYPE = 'DirectObjectStairsType';
    const ENTRANCE_ACCESSIBILITY_NOELEVATION = 'NoelevationEntranceAccessibility';
    const ENTRANCE_ACCESSIBILITY_RAMP = 'RampEntranceAccessibility';
    const ENTRANCE_ACCESSIBILITY_ONE_STEP = 'OneStepEntranceAccessibility';
    const ENTRANCE_ACCESSIBILITY_MORE_STEPS = 'MoreStepsEntranceAccessibility';
    const ENTRANCE_ACCESSIBILITY_PLATFORM = 'PlatformEntranceAccessibility';
    const ENTRANCE_TYPE_1 = 'mainEntrance';
    const ENTRANCE_TYPE_2 = 'sideEntrance';

    const WC_ACCESSIBILITY_OK = 'AccessibleWCMKPO';
    const WC_ACCESSIBILITY_PARTLY = 'PartlyAccessibleWCMKPO';
    const WC_ACCESSIBILITY_NO = 'InAccessibleWCMKPO';

    const WC_LOCALIZATION_SELF = 'SelfcontainedWCCabinLocalization';
    const WC_LOCALIZATION_LADIES = 'LadiesWCCabinLocalization';
    const WC_DOOR_OPENING_DIRECTION = 'OutwardsDoorOpeningDirection';
    const WC_WASHBASIN_UNDERPASS_SUFFICIENT = 'SufficientWashbasinUnderpass';
    const WC_BASIN_SPACE_FREE = 'FreeWCBasinSpace';

    const OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE = 'EntireObjectInteriorAccessibility';
    const OBJECT_INTERIOR_ACCESSIBILITY_PART = 'PartObjectInteriorAccessibility';
    const OBJECT_INTERIOR_ACCESSIBILITY_INACCESSIBLE = 'InaccessibleObjectInteriorAccessibility';

    const CITY = 'city';
    const CITY_PART = 'cityPart';
    const ZIP_CODE = 'zipcode';

    const OBJECT = 'object';
    const RAMP_SKIDS = 'rampskids';
    const PLATFORM = 'platform';
    const ELEVATOR = 'elevator';
    const WC = 'wc';

    const IMAGE = 'image';

    public static $ENUM_COLUMN_TABLE_MAPPING = [
        self::OBJECT => [
            'accessibility' => 'accessibility',
            'accessibility_pram' => 'accessibility',
            'accessibility_seniors' => 'accessibility',
            'source' => 'exchange_source',
            'entrance1_accessibility' => 'entrance_accessibility',
            'entrance1_bell_type' => 'bell_type',
            'entrance1_door1_opening_direction' => 'door_opening_direction',
            'entrance1_door1_opening' => 'door_opening',
            'entrance1_door1_type' => 'door_type',
            'entrance1_door2_opening_direction' => 'door_opening_direction',
            'entrance1_door2_opening' => 'door_opening',
            'entrance1_door2_type' => 'door_type',
            'entrance1_guidingline' => 'entrance_guidingline',
            'entrance1_contrast_marking_localization' => 'contrast_marking_localization',
            'entrance2_access' => 'mappable_entity_access',
            'entrance2_accessibility' => 'entrance_accessibility',
            'entrance2_bell_type' => 'bell_type',
            'entrance2_door1_opening_direction' => 'door_opening_direction',
            'entrance2_door1_opening' => 'door_opening',
            'entrance2_door1_type' => 'door_type',
            'entrance2_door2_opening_direction' => 'door_opening_direction',
            'entrance2_door2_opening' => 'door_opening',
            'entrance2_door2_type' => 'door_type',
            'entrance2_guidingline' => 'entrance_guidingline',
            'entrance2_contrast_marking_localization' => 'contrast_marking_localization',
            'license' => 'license',
            'object_interior_accessibility' => 'object_interior_accessibility',
            'object_stairs_type' => 'object_stairs_type',
            'object_type' => 'object_type',
            'object_contrast_marking_localization' => 'contrast_marking_localization',
        ],
        self::RAMP_SKIDS => [
            'ramp_relation' => 'rampskids_platform_relation',
            'ramp_localization' => 'ramp_skids_localization',
            'ramp_mobility' => 'ramp_skids_mobility',
            'ramp_type' => 'ramp_type',
            'ramp_surface' => 'ramp_surface',
            'ramp_handle_orientation' => 'ramp_handle_localization',
            'skids_localization' => 'ramp_skids_localization',
            'skids_mobility' => 'ramp_skids_mobility',
        ],
        self::PLATFORM => [
            'platform_relation' => 'rampskids_platform_relation',
            'platform_access' => 'mappable_entity_access',
            'platform_type' => 'platform_type',
            'entryarea1_entry' => 'entryarea_entry',
            'entryarea1_bell_type' => 'bell_type',
            'entryarea2_entry' => 'entryarea_entry',
            'entryarea2_bell_type' => 'bell_type',
        ],
        self::ELEVATOR => [
            'elevator_access' => 'mappable_entity_access',
            'elevator_type' => 'elevator_type',
            'elevator_driveoff' => 'elevator_driveoff',
            'elevator_control1_relief_marking' => 'elevator_control_relief_marking',
            'elevator_control1_flat_marking' => 'elevator_control_flat_marking',
            'elevator_a_o_b_announcements_scheme' => 'a_o_b_announcement',
            'elevator_cage_seconddoor_localization' => 'elevator_cage_seconddoor_localization',
            'elevator_control2_relief_marking' => 'elevator_control_relief_marking',
            'elevator_control2_flat_marking' => 'elevator_control_flat_marking',
            'elevator_handle_localization' => 'elevator_handle_localization',
            'elevator_cage_mirror_localization' => 'elevator_cage_mirror_localization',
            'door1_opening' => 'door_opening',
            'door2_opening' => 'door_opening'
        ],
        self::WC => [
            'wc_accessibility' => 'w_c_categorization',
            'wc_cabin_access' => 'mappable_entity_wc_cabin_access',
            'wc_cabin_localization' => 'w_c_cabin_localization',
            'wc_switch' => 'w_c_switch',
            'wc_flushing' => 'w_c_flushing',
            'wc_flushing_difficulty' => 'w_c_flushing_difficulty',
            'wc_basin_space' => 'w_c_basin_space',
            'wc_changingdesk' => 'w_c_changingdesk',
            'wc_cabin_door_disposition' => 'w_c_cabin_disposition',
            'wc_cabin_w_c_basin_disposition' => 'w_c_cabin_disposition',
            'wc_cabin_wash_basin_disposition' => 'w_c_cabin_disposition',
            'hallway1_door_marking' => 'hallway_door_marking',
            'hallway2_door_marking' => 'hallway_door_marking',
            'handle1_type' => 'handle_type',
            'handle2_type' => 'handle_type',
            'door_opening_direction' => 'w_c_door_opening_direction',
            'door_handle_position' => 'w_c_door_handle_position',
            'washbasin_underpass' => 'washbasin_underpass',
            'washbasin_handle_type' => 'washbasin_handle_type',
            'tap_type' => 'tap_type',
        ],
    ];

    public static $LANG_AWARE_COLUMNS = [
        self::OBJECT => [
            'title',
            'description',
            'object_type_custom',
            'entrance1_reserved_parking_localization',
            'entrance1_reserved_parking_access_description',
            'entrance1_longitudinal_inclination_localization',
            'entrance1_transverse_inclination_localization',
            'entrance1_difficult_surface_description',
            'entrance1_aob_localization',
            'entrance1_has_description',
            'entrance1_has_notes',
            'entrance2_localization',
            'entrance2_access_provided_by',
            'entrance2_reserved_parking_localization',
            'entrance2_reserved_parking_access_description',
            'entrance2_longitudinal_inclination_localization',
            'entrance2_transverse_inclination_localization',
            'entrance2_difficult_surface_description',
            'entrance2_aob_localization',
            'entrance2_has_description',
            'entrance2_has_notes',
            'object_steps1_localization',
            'object_narrowed_passage_localization',
            'object_tourniquet_localization',
            'object_navigation_system_description',
            'object_has_description',
            'object_has_notes',
        ],
        self::RAMP_SKIDS => [
            'ramp_interior_localization',
            'ramp_access_provided_by',
            'skids_interior_localization',
            'ramp_skids_has_notes',
            'ramp_skids_has_description',
        ],
        self::PLATFORM => [
            'platform_localization',
            'platform_has_notes',
            'platform_has_description',
        ],
        self::ELEVATOR => [
            'elevator_localization',
            'elevator_access_provided_by',
            'elevator_connects_floors',
            'elevator_aob_localization',
            'elevator_has_notes',
            'elevator_has_description',
        ],
        self::WC => [
            'wc_localization',
            'wc_has_notes',
            'wc_has_description',
            'wc_access_provided_by',
        ],
    ];
}
