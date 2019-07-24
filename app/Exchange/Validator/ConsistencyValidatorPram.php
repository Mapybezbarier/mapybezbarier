<?php

namespace MP\Exchange\Validator;

use MP\Object\ObjectMetadata;
use MP\Util\Arrays;

/**
 * Validator konzistence objektu dle pristupnosti pro rodice s detmi.
 */
class ConsistencyValidatorPram extends ConsistencyValidatorDefault
{
    const OK_DOOR_WIDTH = 70;
    const OK_DOOR_STEP_HEIGHT = 7;
    const OK_ELEVATOR_DOOR1_WIDTH = 70;
    const OK_ELEVATOR_DOOR2_WIDTH = 70;
    const OK_ELEVATOR_CAGE_WIDTH = 100;
    const OK_ELEVATOR_CAGE_DEPTH = 110;
    const OK_PLATFORM_ENTRYAREA_WIDTH = 70;
    const OK_PLATFORM_WIDTH = 70;
    const OK_PLATFORM_DEPTH = 90;
    const PARTLY_DOOR_STEP_HEIGHT = 15;
    const PARTLY_ELEVATOR_DOOR1_WIDTH = 70;
    const PARTLY_ELEVATOR_DOOR2_WIDTH = 70;
    const PARTLY_ELEVATOR_CAGE_WIDTH = 80;
    const PARTLY_ELEVATOR_CAGE_DEPTH = 100;

    const VALIDATOR_NAME = 'consistencyPram';

    /** @var array */
    protected $object;

    /**
     * @param array $object
     */
    public function validate(array $object)
    {
        // Konzistenci pro komunitni data vubec nekontroluji
        if ($object['certified'] && isset($object['accessibilityPram'])) {
            $this->object = $object;

            if (ObjectMetadata::ACCESSIBILITY_OK === $this->object['accessibilityPram']) {
                $this->checkAccessibilityOk();
            } else if (ObjectMetadata::ACCESSIBILITY_PARTLY === $this->object['accessibilityPram']) {
                $this->checkAccessibilityPartly();
            }
        }
    }

    /**
     * Kontrola, zda jednotlive parametry splnuji pozadavky na plne pristupny objekt
     */
    protected function checkAccessibilityOk()
    {
        // 1. vstupy
        $check = $this->checkEntrance();

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'object1');
        }

        // 2. rampy/liziny
        $check = $this->checkRampSkids(
            static::OK_RAMP_MAX_LENGTH_1, static::OK_RAMP_MAX_INCLINATION_1,
            static::OK_RAMP_MAX_LENGTH_2, static::OK_RAMP_MAX_INCLINATION_2,
            static::OK_RAMP_MIN_WIDTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'ramps1');
        }

        // 3. dvere
        $check = $this->checkDoors(['mainpanel', 'sidepanel'], static::OK_DOOR_WIDTH, false);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doors2');
        }

        // 4. prahy
        $check = $this->checkDoorSteps(static::OK_DOOR_STEP_HEIGHT);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doorsteps2');
        }

        // 5. vstup - schody
        $validTypes = [
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION, ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP,
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_ONE_STEP,
        ];
        $check = $this->checkEntranceSteps($validTypes);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'object2');
        }

        // 6. vytahy
        $check = $this->checkElevators(
            static::OK_ELEVATOR_DOOR1_WIDTH, static::OK_ELEVATOR_DOOR2_WIDTH,
            static::OK_ELEVATOR_CAGE_WIDTH, static::OK_ELEVATOR_CAGE_DEPTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'elevators2');
        }

        // 6. plosiny
        $check = $this->checkPlatforms(
            static::OK_PLATFORM_ENTRYAREA_WIDTH, static::OK_PLATFORM_WIDTH, static::OK_PLATFORM_DEPTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'platforms2');
        }

        // 7. zachody
        $check = $this->checkWcs();

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wcs3');
        }
    }

    /**
     * Kontrola, zda jednotlive parametry splnuji pozadavky na castecne pristupny objekt
     */
    protected function checkAccessibilityPartly()
    {
        // 1. interier
        $check = $this->checkInterior();

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'interior2');
        }

        // 2. rampy/liziny
        $check = $this->checkRampSkids(
            static::PARTLY_RAMP_MAX_LENGTH_1, static::PARTLY_RAMP_MAX_INCLINATION_1,
            static::PARTLY_RAMP_MAX_LENGTH_2, static::PARTLY_RAMP_MAX_INCLINATION_2,
            static::PARTLY_RAMP_MIN_WIDTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'ramps2');
        }

        // 3. dvere
        $check = $this->checkDoors(['mainpanel', 'sidepanel'], static::PARTLY_DOOR_WIDTH, false);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doors2');
        }

        // 4. prahy
        $check = $this->checkDoorSteps(static::PARTLY_DOOR_STEP_HEIGHT);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doorsteps3');
        }

        // 5. vytahy
        $check = $this->checkElevators(
            static::PARTLY_ELEVATOR_DOOR1_WIDTH, static::PARTLY_ELEVATOR_DOOR2_WIDTH,
            static::PARTLY_ELEVATOR_CAGE_WIDTH, static::PARTLY_ELEVATOR_CAGE_DEPTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'elevators3');
        }

        // 6. plosiny
        $check = $this->checkPlatforms(
            static::PARTLY_PLATFORM_ENTRYAREA_WIDTH, static::PARTLY_PLATFORM_WIDTH, static::PARTLY_PLATFORM_DEPTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'platforms2');
        }

        // 7. vstup - schody
        $validTypes = [
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION, ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP,
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_ONE_STEP, ObjectMetadata::ENTRANCE_ACCESSIBILITY_PLATFORM,
        ];
        $check = $this->checkEntranceSteps($validTypes);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'object2');
        }
    }

    /**
     * @return bool
     */
    protected function checkWcs()
    {
        $wcs = Arrays::get($this->object, ObjectMetadata::WC, []);

        if ($wcs) {
            $ret = false;

            foreach ($wcs as $wc) {
                $wcIsChangingdesk = Arrays::get($wc, 'wcIsChangingdesk', null);

                if ($wcIsChangingdesk === true) {
                    $ret = true;
                    break;
                }
            }
        } else {
            $ret = true;
        }

        return $ret;
    }
}
