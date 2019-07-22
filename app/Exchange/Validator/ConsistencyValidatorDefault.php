<?php

namespace MP\Exchange\Validator;

use MP\Exchange\Service\ImportLogger;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;

/**
 * Validator konzistence objektu.
 */
class ConsistencyValidatorDefault implements IValidator
{
    const OK_RAMP_MAX_LENGTH_1 = 300;
    const OK_RAMP_MAX_INCLINATION_1 = 12.5;
    const OK_RAMP_MAX_LENGTH_2 = 900;
    const OK_RAMP_MAX_INCLINATION_2 = 8.0;
    const OK_RAMP_MIN_WIDTH = 110;
    const PARTLY_RAMP_MAX_LENGTH_1 = 300;
    const PARTLY_RAMP_MAX_INCLINATION_1 = 16.5;
    const PARTLY_RAMP_MAX_LENGTH_2 = 900;
    const PARTLY_RAMP_MAX_INCLINATION_2 = 12.5;
    const PARTLY_RAMP_MIN_WIDTH = 110;
    const OK_DOOR_WIDTH = 80;
    const OK_DOOR_STEP_HEIGHT = 2;
    const OK_ELEVATOR_DOOR1_WIDTH = 80;
    const OK_ELEVATOR_DOOR2_WIDTH = 80;
    const OK_ELEVATOR_CAGE_WIDTH = 100;
    const OK_ELEVATOR_CAGE_DEPTH = 125;
    const PARTLY_DOOR_WIDTH = 70;
    const PARTLY_DOOR_STEP_HEIGHT = 7;
    const PARTLY_ELEVATOR_DOOR1_WIDTH = 70;
    const PARTLY_ELEVATOR_DOOR2_WIDTH = 70;
    const PARTLY_ELEVATOR_CAGE_WIDTH = 100;
    const PARTLY_ELEVATOR_CAGE_DEPTH = 110;
    const PARTLY_PLATFORM_ENTRYAREA_WIDTH = 70;
    const PARTLY_PLATFORM_WIDTH = 70;
    const PARTLY_PLATFORM_DEPTH = 90;
    const OK_WC_DOOR_WIDTH = 80;
    const OK_WC_CABIN_SIZE = 160;
    const OK_WC_BASIN_DISTANCE = 80;
    const PARTLY_WC_DOOR_WIDTH = 70;
    const PARTLY_WC_CABIN_SIZE = 140;
    const PARTLY_WC_BASIN_DISTANCE = 70;

    const VALIDATOR_NAME = 'consistencyDefault';

    /** @var array */
    protected $object;

    /**
     * @param array $object
     */
    public function validate(array $object)
    {
        // Konzistenci pro komunitni data vubec nekontroluji
        if ($object['certified']) {
            $this->object = $object;

            if (ObjectMetadata::ACCESSIBILITY_OK === $this->object['accessibility']) {
                $this->checkAccessibilityOk();
            } else if (ObjectMetadata::ACCESSIBILITY_PARTLY === $this->object['accessibility']) {
                $this->checkAccessibilityPartly();
            }

            $i = 1;

            foreach (Arrays::get($this->object, ObjectMetadata::WC, []) as $wc) {
                if (isset($wc['wcAccessibility'])) {
                    if (ObjectMetadata::WC_ACCESSIBILITY_OK === $wc['wcAccessibility']) {
                        $this->checkWcAccessibility1($wc, $i);
                    } else if (ObjectMetadata::WC_ACCESSIBILITY_PARTLY === $wc['wcAccessibility']) {
                        $this->checkWcAccessibility2($wc, $i);
                    }
                }

                $i++;
            }
        }
    }

    /**
     * @return string|null
     */
    public function getFormat()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return IValidator::TYPE_CONSISTENCY;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::VALIDATOR_NAME;
    }

    /**
     * Zaloguje notice z kontroly konzistence - ve vypise bude oddeleno
     *
     * @param array $object
     * @param string $message
     * @param array $arguments
     */
    public static function addConsistencyNotice($object, $message, $arguments = [])
    {
        $message = 'consistency.'.$message;
        ImportLogger::addNotice($object, $message, $arguments, static::VALIDATOR_NAME);
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
        $check = $this->checkDoors(['mainpanel'], static::OK_DOOR_WIDTH, true);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doors1');
        }

        // 4. prahy
        $check = $this->checkDoorSteps(static::OK_DOOR_STEP_HEIGHT);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'doorsteps1');
        }

        // 5. vytahy
        $check = $this->checkElevators(
            static::OK_ELEVATOR_DOOR1_WIDTH, static::OK_ELEVATOR_DOOR2_WIDTH,
            static::OK_ELEVATOR_CAGE_WIDTH, static::OK_ELEVATOR_CAGE_DEPTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'elevators1');
        }

        // 6. zachody
        $check = $this->checkWcs();

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wcs1');
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
            $this->addConsistencyNotice($this->object, 'doorsteps2');
        }

        // 5. vytahy
        $check = $this->checkElevators(
            static::PARTLY_ELEVATOR_DOOR1_WIDTH, static::PARTLY_ELEVATOR_DOOR2_WIDTH,
            static::PARTLY_ELEVATOR_CAGE_WIDTH, static::PARTLY_ELEVATOR_CAGE_DEPTH
        );

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'elevators2');
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
     * Kontrola, zda jednotlive parametry splnuji pozadavky na plne pristupne WC
     *
     * @param array $wc
     * @param int $wcNo ke kolikate priloze WC se kontrola vaze
     */
    protected function checkWcAccessibility1($wc, $wcNo)
    {
        // 1. umisteni
        $check = $this->checkWcLocalization($wc);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.location1', ['key' => $wcNo]);
        }

        // 2. dvere
        $check = $this->checkWcDoors($wc, static::OK_WC_DOOR_WIDTH);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.doors1', ['key' => $wcNo]);
        }

        // 3. kabina
        $check = $this->checkWcCabin($wc, static::OK_WC_CABIN_SIZE);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.cabin1', ['key' => $wcNo]);
        }

        // 4. misa
        $check = $this->checkWcBasin($wc, static::OK_WC_BASIN_DISTANCE);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.basin1', ['key' => $wcNo]);
        }

        // 5. madla
        $check = $this->checkWcHandles($wc);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.handles1', ['key' => $wcNo]);
        }

        // 6. umyvadlo
        $check = $this->checkWcWashBasin($wc);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.washbasin1', ['key' => $wcNo]);
        }

        // 7. manipulacni prostor
        $check = $this->checkWcSpace($wc);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.space1', ['key' => $wcNo]);
        }
    }

    /**
     * Kontrola, zda jednotlive parametry splnuji pozadavky na castecne pristupne WC
     *
     * @param array $wc
     * @param int $wcNo ke kolikate priloze WC se kontrola vaze
     */
    protected function checkWcAccessibility2($wc, $wcNo)
    {
        // 1. dvere
        $check = $this->checkWcDoors($wc, static::PARTLY_WC_DOOR_WIDTH);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.doors2', ['key' => $wcNo]);
        }

        // 2. kabina
        $check = $this->checkWcCabin($wc, static::PARTLY_WC_CABIN_SIZE);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.cabin2', ['key' => $wcNo]);
        }

        // 3. misa
        $check = $this->checkWcBasin($wc, static::PARTLY_WC_BASIN_DISTANCE);

        if (!$check) {
            $this->addConsistencyNotice($this->object, 'wc.basin2', ['key' => $wcNo]);
        }
    }

    /**
     * @return bool
     */
    protected function checkEntrance()
    {
        $entrance1Accessibility = Arrays::get($this->object, 'entrance1Accessibility', null);
        $entrance2Accessibility = Arrays::get($this->object, 'entrance2Accessibility', null);
        $objectInteriorAccessibility = Arrays::get($this->object, 'objectInteriorAccessibility', null);

        if (isset($entrance1Accessibility) || isset($entrance2Accessibility) || isset($objectInteriorAccessibility)) {
            $validTypes = [ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION, ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP];

            $checkEntrance = (
                (ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE === $objectInteriorAccessibility)
                && (
                    in_array($entrance1Accessibility, $validTypes, true)
                    || in_array($entrance2Accessibility, $validTypes, true)
                )
            );
        } else {
            $checkEntrance = true;
        }

        return $checkEntrance;
    }

    /**
     * @param integer $length1
     * @param float $inclination1
     * @param integer $length2
     * @param float $inclination2
     * @param integer $width
     *
     * @return bool
     */
    protected function checkRampSkids($length1, $inclination1, $length2, $inclination2, $width)
    {
        $ret = true;

        foreach (Arrays::get($this->object, ObjectMetadata::RAMP_SKIDS, []) as $rampSkids) {
            list($maxLength, $maxInclination, $minWidth) = $this->getExtremeRampSkidsValues($rampSkids);
            $checkWidth = null === $minWidth || $minWidth >= $width;

            if ($maxLength <= $length1) {
                $ret = ($maxInclination <= $inclination1) && $checkWidth;
            } else if ($maxLength <= $length2) {
                $ret = ($maxInclination <= $inclination2) && $checkWidth;
            }

            if (!$ret) {
                break;
            }
        }

        return $ret;
    }

    /**
     * Vypocet max. delky a sklonu ramena/lizin + min. sirky
     *
     * @param array $rampSkids
     *
     * @return array 3prvkove pole delka, sklon, sirka
     */
    protected function getExtremeRampSkidsValues($rampSkids)
    {
        $maxInclination = $maxLength = 0;
        $minWidth = null;

        for ($i = 1; $i <= 4; $i++) {
            if (Arrays::get($rampSkids, "rampleg{$i}Inclination", 0) > $maxInclination) {
                $maxInclination = $rampSkids["rampleg{$i}Inclination"];
            }

            if (Arrays::get($rampSkids, "rampleg{$i}Length", 0) > $maxLength) {
                $maxLength = $rampSkids["rampleg{$i}Length"];
            }

            $ramplegWidth = Arrays::get($rampSkids, "rampleg{$i}Width", null);

            if (null !== $ramplegWidth && (null === $minWidth || $ramplegWidth < $minWidth)) {
                $minWidth = $ramplegWidth;
            }
        }

        if (Arrays::get($rampSkids, 'skidsInclination', 0) > $maxInclination) {
            $maxInclination = $rampSkids['skidsInclination'];
        }

        if (Arrays::get($rampSkids, 'skidsLength', 0) > $maxLength) {
            $maxLength = $rampSkids['skidsLength'];
        }

        return [$maxLength, $maxInclination, $minWidth];
    }

    /**
     * @param array $panels jake casti dveri scitam
     * @param int $width min. sirka
     * @param bool $narrowedPassageRestricted je zakazane mit zuzeny pruchod?
     *
     * @return bool
     */
    protected function checkDoors($panels, $width, $narrowedPassageRestricted)
    {
        $ret = true;

        $doors = ['entrance1Door1', 'entrance1Door2', 'entrance2Door1', 'entrance2Door2'];

        foreach ($doors as $doorPrefix) {
            $totalWidth = null;

            foreach ($panels as $panel) {
                $panelWidth = Arrays::get($this->object, "{$doorPrefix}{$panel}Width", null);

                if (null !== $panelWidth) {
                    $totalWidth += $panelWidth;
                }
            }

            if (null !== $totalWidth && $totalWidth < $width) {
                $ret = false;
                break;
            }
        }

        if ($ret) {
            if ($narrowedPassageRestricted && Arrays::get($this->object, "objectIsNarrowedPassage", false)) {
                $ret = false;
            }

            $narrowdPassageWidth = Arrays::get($this->object, "objectNarrowedPassageWidth", null);

            if (null !== $narrowdPassageWidth && $narrowdPassageWidth < $width) {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * @param integer $height
     *
     * @return bool
     */
    protected function checkDoorSteps($height)
    {
        $ret = true;

        $doors = ['entrance1Door1', 'entrance1Door2', 'entrance2Door1', 'entrance2Door2'];

        foreach ($doors as $doorPrefix) {
            $stepHeight = Arrays::get($this->object, "{$doorPrefix}StepHeight", null);

            if (null !== $stepHeight && $stepHeight > $height) {
                $ret = false;
                break;
            }
        }

        return $ret;
    }

    /**
     * @param $door1Width
     * @param $door2Width
     * @param $cageWidth
     * @param $cageDepth
     *
     * @return bool
     */
    protected function checkElevators($door1Width, $door2Width, $cageWidth, $cageDepth)
    {
        $ret = true;

        foreach (Arrays::get($this->object, ObjectMetadata::ELEVATOR, []) as $elevator) {
            $width = Arrays::get($elevator, "door1Width", null);

            if (null !== $width && $door1Width > $width) {
                $ret = false;
                break;
            }

            $width = Arrays::get($elevator, "door2Width", null);

            if (null !== $width && $door2Width > $width) {
                $ret = false;
                break;
            }

            $width = Arrays::get($elevator, "elevatorCageWidth", null);

            if (null !== $width && $cageWidth > $width) {
                $ret = false;
                break;
            }

            $depth = Arrays::get($elevator, "elevatorCageDepth", null);

            if (null !== $depth && $cageDepth > $depth) {
                $ret = false;
                break;
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
                $wcAccessibility = Arrays::get($wc, "wcAccessibility", null);

                if (in_array($wcAccessibility, [ObjectMetadata::WC_ACCESSIBILITY_OK, ObjectMetadata::WC_ACCESSIBILITY_PARTLY], true)) {
                    $ret = true;
                    break;
                }
            }
        } else {
            $ret = true;
        }

        return $ret;
    }

    /**
     * @return bool
     */
    protected function checkInterior()
    {
        $ret = true;
        $objectInteriorAccessibility = Arrays::get($this->object, 'objectInteriorAccessibility', null);

        if (isset($objectInteriorAccessibility)) {
            $ret = in_array(
                $objectInteriorAccessibility,
                [ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE, ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_PART],
                true
            );
        }

        return $ret;
    }

    /**
     * @param int $entryAreaWidth
     * @param int $platformWidth
     * @param int $platformDepth
     *
     * @return bool
     */
    protected function checkPlatforms($entryAreaWidth, $platformWidth, $platformDepth)
    {
        $ret = true;

        foreach (Arrays::get($this->object, ObjectMetadata::PLATFORM, []) as $platform) {
            $width = Arrays::get($platform, "entryarea1Width", null);

            if (null !== $width && $entryAreaWidth > $width) {
                $ret = false;
                break;
            }

            $width = Arrays::get($platform, "entryarea2Width", null);

            if (null !== $width && $entryAreaWidth > $width) {
                $ret = false;
                break;
            }

            $width = Arrays::get($platform, "platformWidth", null);

            if (null !== $width && $platformWidth > $width) {
                $ret = false;
                break;
            }

            $depth = Arrays::get($platform, "platformDepth", null);

            if (null !== $depth && $platformDepth > $depth) {
                $ret = false;
                break;
            }
        }

        return $ret;
    }

    /**
     * Kontrola schodu u vstupu
     * s vyberem povolenych typu vstupu a volitelnym omezenim na pocet schodu a vysku jednoho schodu
     * @return bool
     */
    protected function checkEntranceSteps(array $validTypes, ?int $maxStepsCount = null, ?int $maxOneStepHeight = null) : bool
    {
        $ret = true;

        $entrance1Accessibility = Arrays::get($this->object, 'entrance1Accessibility', null);
        $entrance2Accessibility = Arrays::get($this->object, 'entrance2Accessibility', null);

        if (isset($entrance1Accessibility) || isset($entrance2Accessibility)) {
            $ret = in_array($entrance1Accessibility, $validTypes, true) || in_array($entrance2Accessibility, $validTypes, true);

            if ($ret && $maxStepsCount !== null) {
                $entrance1Steps1NumberOf = Arrays::get($this->object, 'entrance1Steps1NumberOf', null);
                $entrance2Steps1NumberOf = Arrays::get($this->object, 'entrance2Steps1NumberOf', null);

                if (
                    $ret && $entrance1Accessibility === ObjectMetadata::ENTRANCE_ACCESSIBILITY_MORE_STEPS
                    && ($entrance1Steps1NumberOf === null || $entrance1Steps1NumberOf > $maxStepsCount)
                ) {
                    $ret = false;
                }

                if (
                    $ret && $entrance2Accessibility === ObjectMetadata::ENTRANCE_ACCESSIBILITY_MORE_STEPS
                    && ($entrance2Steps1NumberOf === null || $entrance2Steps1NumberOf > $maxStepsCount)
                ) {
                    $ret = false;
                }
            }

            if ($ret && $maxOneStepHeight !== null) {
                $entrance1Steps1Height = Arrays::get($this->object, 'entrance1Steps1Height', null);
                $entrance2Steps1Height = Arrays::get($this->object, 'entrance2Steps1Height', null);

                if (
                    $ret && $entrance1Accessibility === ObjectMetadata::ENTRANCE_ACCESSIBILITY_ONE_STEP
                    && ($entrance1Steps1Height === null || $entrance1Steps1Height > $maxOneStepHeight)
                ) {
                    $ret = false;
                }

                if (
                    $ret && $entrance2Accessibility === ObjectMetadata::ENTRANCE_ACCESSIBILITY_ONE_STEP
                    && ($entrance2Steps1Height === null || $entrance2Steps1Height > $maxOneStepHeight)
                ) {
                    $ret = false;
                }
            }
        }

        return $ret;
    }

    /**
     * @param array $wc
     *
     * @return bool
     */
    protected function checkWcLocalization($wc)
    {
        $localization = Arrays::get($wc, 'wcCabinLocalization', null);

        return in_array(
            $localization,
            [ObjectMetadata::WC_LOCALIZATION_SELF, ObjectMetadata::WC_LOCALIZATION_LADIES],
            true
        );
    }

    /**
     * @param array $wc
     * @param int $width
     *
     * @return bool
     */
    protected function checkWcDoors($wc, $width)
    {
        $ret = true;

        $doors = ['hallway1DoorWidth', 'hallway2DoorWidth', 'door'];

        foreach ($doors as $doorPrefix) {
            $doorWidth = Arrays::get($wc, "{$doorPrefix}Width", null);

            if (null !== $doorWidth && $doorWidth < $width) {
                $ret = false;
                break;
            }
        }

        if ($ret) {
            $direction = Arrays::get($wc, "doorOpeningDirection", null);
            $ret = ObjectMetadata::WC_DOOR_OPENING_DIRECTION === $direction;
        }

        return $ret;
    }

    /**
     * @param array $wc
     * @param int $size
     *
     * @return bool
     */
    protected function checkWcCabin($wc, $size)
    {
        $cabinDepth = Arrays::get($wc, "wcCabinDepth", null);
        $cabinWidth = Arrays::get($wc, "wcCabinWidth", null);

        return ($cabinDepth >= $size && $cabinWidth >= $size);
    }

    /**
     * @param array $wc
     * @param int $distance
     *
     * @return bool
     */
    protected function checkWcBasin($wc, $distance)
    {
        $left = Arrays::get($wc, "wcBasinLeftDistance", null);
        $right = Arrays::get($wc, "wcBasinRightDistance", null);

        return ($left >= $distance || $right >= $distance);
    }

    /**
     * @param array $wc
     *
     * @return bool
     */
    protected function checkWcHandles($wc)
    {
        return (
            Arrays::get($wc, "wcBasinIsPaperReach", false)
            && Arrays::get($wc, "handle1Type", false)
            && Arrays::get($wc, "handle2Type", false)
        );
    }

    /**
     * @param array $wc
     *
     * @return bool
     */
    protected function checkWcWashBasin($wc)
    {
        return (ObjectMetadata::WC_WASHBASIN_UNDERPASS_SUFFICIENT === Arrays::get($wc, "washbasinUnderpass", null));
    }

    /**
     * @param array $wc
     *
     * @return bool
     */
    protected function checkWcSpace($wc)
    {
        return (ObjectMetadata::WC_BASIN_SPACE_FREE === Arrays::get($wc, "wcBasinSpace", null));
    }
}
