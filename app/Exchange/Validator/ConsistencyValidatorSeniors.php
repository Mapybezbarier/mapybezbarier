<?php

namespace MP\Exchange\Validator;

use MP\Object\ObjectMetadata;
use MP\Util\Arrays;

/**
 * Validator konzistence objektu dle pristupnosti pro seniory.
 */
class ConsistencyValidatorSeniors extends ConsistencyValidatorDefault
{
    const OK_DOOR_WIDTH = 70;
    const OK_DOOR_STEP_HEIGHT = 7;
    const OK_ELEVATOR_DOOR1_WIDTH = 70;
    const OK_ELEVATOR_DOOR2_WIDTH = 70;
    const OK_ELEVATOR_CAGE_WIDTH = 100;
    const OK_ELEVATOR_CAGE_DEPTH = 110;
    const OK_WC_BASIN_SEAT_HEIGHT = 45;
    const PARTLY_DOOR_WIDTH = 60;
    const PARTLY_DOOR_STEP_HEIGHT = 15;
    const PARTLY_ELEVATOR_DOOR1_WIDTH = 60;
    const PARTLY_ELEVATOR_DOOR2_WIDTH = 60;
    const PARTLY_ELEVATOR_CAGE_WIDTH = 80;
    const PARTLY_ELEVATOR_CAGE_DEPTH = 100;
    const PARTLY_ENTRANCE_MAX_STEPS = 2;
    const VALIDATOR_NAME = 'consistencySeniors';

    /** @var array */
    protected $object;

    /**
     * @param array $object
     */
    public function validate(array $object)
    {
        // Konzistenci pro komunitni data vubec nekontroluji
        if ($object['certified'] && isset($object['accessibilitySeniors'])) {
            $this->object = $object;

            if (ObjectMetadata::ACCESSIBILITY_OK === $this->object['accessibilitySeniors']) {
                $this->checkAccessibilityOk();
            } else if (ObjectMetadata::ACCESSIBILITY_PARTLY === $this->object['accessibilitySeniors']) {
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

        // 2. zabradli u vnitrniho schodiste
        $check = $this->checkObjectStairsBannister();

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'stairsbannister1');
        }

        // 3. rampy/liziny + dodatecna kontrola na zabradli
        $check = $this->checkRampSkids(
            static::OK_RAMP_MAX_LENGTH_1, static::OK_RAMP_MAX_INCLINATION_1,
            static::OK_RAMP_MAX_LENGTH_2, static::OK_RAMP_MAX_INCLINATION_2,
            static::OK_RAMP_MIN_WIDTH
        );

        if ($check) {
            $check = $this->checkRampSkidsAdditional();
        }

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'ramps1');
        }

        // 4. dvere
        $check = $this->checkDoors(['mainpanel', 'sidepanel'], static::OK_DOOR_WIDTH, false);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doors2');
        }

        // 5. prahy
        $check = $this->checkDoorSteps(static::OK_DOOR_STEP_HEIGHT);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doorsteps2');
        }

        // 6. vytahy + dodatecna kontrola na sedatko
        $check = $this->checkElevators(
            static::OK_ELEVATOR_DOOR1_WIDTH, static::OK_ELEVATOR_DOOR2_WIDTH,
            static::OK_ELEVATOR_CAGE_WIDTH, static::OK_ELEVATOR_CAGE_DEPTH
        );

        if ($check) {
            $check = $this->checkElevatorsAdditional(true, true);
        }

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'elevators4');
        }

        // 7. zachody
        $check = $this->checkWcs();

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wcs2');
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
            $this->addConsistencyNotice($this->object, 'doors3');
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

        if ($check) {
            $check = $this->checkElevatorsAdditional(false, true);
        }

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'elevators5');
        }

        // 7. vstup - schody
        $validTypes = [
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION, ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP,
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_ONE_STEP, ObjectMetadata::ENTRANCE_ACCESSIBILITY_MORE_STEPS,
        ];
        $check = $this->checkEntranceSteps($validTypes, static::PARTLY_ENTRANCE_MAX_STEPS);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'object3');
        }
    }

    /**
     * @return bool
     */
    protected function checkObjectStairsBannister()
    {
        $ret = true;

        $objectIsStairs = Arrays::get($this->object, 'objectIsStairs', null);
        $objectStairsIsBannister = Arrays::get($this->object, 'objectStairsIsBannister', null);

        if ($objectIsStairs === true && $objectStairsIsBannister === false) {
            $ret = false;
        }

        return $ret;
    }

    /**
     * @return bool
     */
    protected function checkRampSkidsAdditional()
    {
        $ret = true;

        foreach (Arrays::get($this->object, ObjectMetadata::RAMP_SKIDS, []) as $rampSkids) {
            $rampIsHandle = Arrays::get($rampSkids, 'rampIsHandle', null);

            if ($rampIsHandle !== true) {
                $ret = false;
                break;
            }
        }

        return $ret;
    }

    /**
     * @return bool
     */
    protected function checkElevatorsAdditional(bool $requiredCageSeat, bool $requiredCageHandle) : bool
    {
        $ret = true;

        foreach (Arrays::get($this->object, ObjectMetadata::ELEVATOR, []) as $elevator) {
            if ($requiredCageSeat) {
                $elevatorIsCageSeat = Arrays::get($elevator, 'elevatorIsCageSeat', null);

                if ($elevatorIsCageSeat !== true) {
                    $ret = false;
                    break;
                }
            }

            if ($requiredCageHandle) {
                $elevatorIsCageHandle = Arrays::get($elevator, 'elevatorIsCageHandle', null);

                if ($elevatorIsCageHandle !== true) {
                    $ret = false;
                    break;
                }
            }
        }

        return $ret;
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
                $handle1Type = Arrays::get($wc, "handle1Type", null);
                $handle2Type = Arrays::get($wc, "handle2Type", null);
                $wcIsAlarmbutton = Arrays::get($wc, 'wcIsAlarmbutton', null);
                $washbasinUnderpass = Arrays::get($wc, 'washbasinUnderpass', null);
                $wcBasinSeatHeight = Arrays::get($wc, 'wcBasinSeatHeight', null);

                if (
                    ($handle1Type !== null || $handle2Type !== null)
                    && ($wcIsAlarmbutton === true)
                    && ($washbasinUnderpass === ObjectMetadata::WC_WASHBASIN_UNDERPASS_SUFFICIENT)
                    && ($wcBasinSeatHeight !== null && $wcBasinSeatHeight >= self::OK_WC_BASIN_SEAT_HEIGHT)
                ) {
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
