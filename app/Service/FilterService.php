<?php

namespace MP\Service;

use MP\Manager\AccessibilityManager;
use MP\Manager\ObjectManager;
use MP\Manager\ObjectTypeManager;
use MP\Util\Arrays;

/**
 * Sluzba pro zpracovani a podporu restrikci filtru.
 *
 * Z parametru v GET pripravi restrictor pro dotaz do databaze. Zroven obsahuje podporu pro sestaveni grafickeho filtru.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FilterService
{
    /** @const Typy dat */
    const TYPE_CERTIFIED = 'certified',
        TYPE_OUTDATED = 'outdated',
        TYPE_COMMUNITY = 'community';

    /** @var ObjectManager */
    protected $objectManager;

    /** @var AccessibilityManager */
    protected $accessibilityManager;

    /** @var ObjectTypeManager */
    protected $objectTypeManager;

    /**
     * @param ObjectManager $objectManager
     * @param AccessibilityManager $accessibilityManager
     * @param ObjectTypeManager $objectTypeManager
     */
    public function __construct(ObjectManager $objectManager, AccessibilityManager $accessibilityManager, ObjectTypeManager $objectTypeManager)
    {
        $this->objectManager = $objectManager;
        $this->accessibilityManager = $accessibilityManager;
        $this->objectTypeManager = $objectTypeManager;
    }

    /**
     * Vraci moznosti pristupnosti pro filtr
     *
     * @return array
     */
    public function getAccesibilityValues()
    {
        $accessibility = $this->accessibilityManager->findAll() ?: [];

        return Arrays::pairs($accessibility, 'id', 'title');
    }

    /**
     * Vraci moznosti kategorii pro filtr
     *
     * @return array
     */
    public function getCategoryValues()
    {
        $categories = $this->objectTypeManager->findAll() ?: [];

        return Arrays::pairs($categories, 'id', 'title');
    }

    /**
     * Vrati pocty objektu v kategorii
     *
     * @return array
     */
    public function getCategoryCounts()
    {
        $statistics = $this->objectManager->getTypesStats() ?: [];

        return Arrays::pairs($statistics, 'object_type_id', 'count');
    }

    /**
     * Vraci moznosti typu podkladu pro filtr
     *
     * @return array
     */
    public function getTypeValues()
    {
        $types = [
            self::TYPE_CERTIFIED,
            self::TYPE_OUTDATED,
            self::TYPE_COMMUNITY,
        ];

        return array_combine($types, $types);
    }
}
