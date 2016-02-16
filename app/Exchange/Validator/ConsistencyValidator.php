<?php

namespace MP\Exchange\Validator;

use MP\Exchange\Service\ImportLogger;
use MP\Object\ObjectMetadata;
use MP\Util\Arrays;

/**
 * Validator konzistence objektu.
 */
class ConsistencyValidator implements IValidator
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
     * Kontrola, zda jednotlive parametry splnuji pozadavky na plne pristupny objekt
     */
    protected function checkAccessibilityOk()
    {
        // 1. vstupy
        $check = $this->checkEntrance();

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'object1');
        }

        // 2. rampy/liziny
        $check = $this->checkRampSkids(
            self::OK_RAMP_MAX_LENGTH_1, self::OK_RAMP_MAX_INCLINATION_1,
            self::OK_RAMP_MAX_LENGTH_2, self::OK_RAMP_MAX_INCLINATION_2,
            self::OK_RAMP_MIN_WIDTH
        );

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'ramps1');
        }

        // 3. dvere
        $check = $this->checkDoors(['mainpanel'], self::OK_DOOR_WIDTH, true);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'doors1');
        }

        // 4. prahy
        $check = $this->checkDoorSteps(self::OK_DOOR_STEP_HEIGHT);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'doorsteps1');
        }

        // 5. vytahy
        $check = $this->checkElevators(
            self::OK_ELEVATOR_DOOR1_WIDTH, self::OK_ELEVATOR_DOOR2_WIDTH,
            self::OK_ELEVATOR_CAGE_WIDTH, self::OK_ELEVATOR_CAGE_DEPTH
        );

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'elevators1');
        }

        // 6. zachody
        $check = $this->checkWcs();

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wcs1');
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
            ImportLogger::addConsistencyNotice($this->object, 'interior2');
        }

        // 2. rampy/liziny
        $check = $this->checkRampSkids(
            self::PARTLY_RAMP_MAX_LENGTH_1, self::PARTLY_RAMP_MAX_INCLINATION_1,
            self::PARTLY_RAMP_MAX_LENGTH_2, self::PARTLY_RAMP_MAX_INCLINATION_2,
            self::PARTLY_RAMP_MIN_WIDTH
        );

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'ramps2');
        }

        // 3. dvere
        $check = $this->checkDoors(['mainpanel', 'sidepanel'], self::PARTLY_DOOR_WIDTH, false);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'doors2');
        }

        // 4. prahy
        $check = $this->checkDoorSteps(self::PARTLY_DOOR_STEP_HEIGHT);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'doorsteps2');
        }

        // 5. vytahy
        $check = $this->checkElevators(
            self::PARTLY_ELEVATOR_DOOR1_WIDTH, self::PARTLY_ELEVATOR_DOOR2_WIDTH,
            self::PARTLY_ELEVATOR_CAGE_WIDTH, self::PARTLY_ELEVATOR_CAGE_DEPTH
        );

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'elevators2');
        }

        // 6. plosiny
        $check = $this->checkPlatforms(
            self::PARTLY_PLATFORM_ENTRYAREA_WIDTH, self::PARTLY_PLATFORM_WIDTH, self::PARTLY_PLATFORM_DEPTH
        );

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'platforms2');
        }

        // 7. vstup - schody
        $check = $this->checkEntranceSteps();

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'object2');
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
            ImportLogger::addConsistencyNotice($this->object, 'wc.location1', ['key' => $wcNo]);
        }

        // 2. dvere
        $check = $this->checkWcDoors($wc, self::OK_WC_DOOR_WIDTH);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.doors1', ['key' => $wcNo]);
        }

        // 3. kabina
        $check = $this->checkWcCabin($wc, self::OK_WC_CABIN_SIZE);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.cabin1', ['key' => $wcNo]);
        }

        // 4. misa
        $check = $this->checkWcBasin($wc, self::OK_WC_BASIN_DISTANCE);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.basin1', ['key' => $wcNo]);
        }

        // 5. madla
        $check = $this->checkWcHandles($wc);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.handles1', ['key' => $wcNo]);
        }

        // 6. umyvadlo
        $check = $this->checkWcWashBasin($wc);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.washbasin1', ['key' => $wcNo]);
        }

        // 7. manipulacni prostor
        $check = $this->checkWcSpace($wc);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.space1', ['key' => $wcNo]);
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
        $check = $this->checkWcDoors($wc, self::PARTLY_WC_DOOR_WIDTH);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.doors2', ['key' => $wcNo]);
        }

        // 2. kabina
        $check = $this->checkWcCabin($wc, self::PARTLY_WC_CABIN_SIZE);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.cabin2', ['key' => $wcNo]);
        }

        // 3. misa
        $check = $this->checkWcBasin($wc, self::PARTLY_WC_BASIN_DISTANCE);

        if (!$check) {
            ImportLogger::addConsistencyNotice($this->object, 'wc.basin2', ['key' => $wcNo]);
        }
    }

    /**
     * @return bool
     */
    protected function checkEntrance()
    {
        $entrance1Accessibility = Arrays::get($this->object, 'entrance1Accessibility', null);
        $entrance2Accessibility = Arrays::get($this->object, 'entrance2Accessibility', null);

        $validTypes = [ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION, ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP];

        $checkEntrance = (
            (ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE === Arrays::get($this->object, 'objectInteriorAccessibility', null))
            && (
                in_array($entrance1Accessibility, $validTypes, true)
                || in_array($entrance2Accessibility, $validTypes, true)
            )
        );

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
        $ret = false;

        foreach (Arrays::get($this->object, ObjectMetadata::WC, []) as $wc) {
            $wcAccessibility = Arrays::get($wc, "wcAccessibility", null);

            if (in_array($wcAccessibility, [ObjectMetadata::WC_ACCESSIBILITY_OK, ObjectMetadata::WC_ACCESSIBILITY_PARTLY])) {
                $ret = true;
                break;
            }
        }

        return $ret;
    }

    /**
     * @return bool
     */
    protected function checkInterior()
    {
        return in_array(
            Arrays::get($this->object, 'objectInteriorAccessibility', null),
            [ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_ENTIRE, ObjectMetadata::OBJECT_INTERIOR_ACCESSIBILITY_PART],
            true
        );
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
     * @return bool
     */
    protected function checkEntranceSteps()
    {
        $entrance1Accessibility = Arrays::get($this->object, 'entrance1Accessibility', null);
        $entrance2Accessibility = Arrays::get($this->object, 'entrance2Accessibility', null);

        $validTypes = [
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_NOELEVATION, ObjectMetadata::ENTRANCE_ACCESSIBILITY_RAMP,
            ObjectMetadata::ENTRANCE_ACCESSIBILITY_ONE_STEP, ObjectMetadata::ENTRANCE_ACCESSIBILITY_PLATFORM,
        ];

        return (
            in_array($entrance1Accessibility, $validTypes, true) || in_array($entrance2Accessibility, $validTypes, true)
        );
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
