<?php

namespace MP\Module\Api\Service;

use MP\Service\FilterService;
use MP\Util\Strings;
use Nette\Http\IRequest;
use Nette\Utils\DateTime;
use Nette\Utils\Validators;

/**
 * Sestavi restriktor pro ziskani mapovych objektu skrze API.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectRestrictorBuilder extends \MP\Service\ObjectRestrictorBuilder
{
    /** @const Nazvy restrikci. */
    const RESTRICTION_CITY = 'city',
        RESTRICTION_CITY_PART = 'city_part',
        RESTRICTION_REGION = 'region',
        RESTRICTION_MODIFIED = 'modified';

    /** @var IRequest */
    protected $request;

    /** @var FilterService */
    protected $filterService;

    /**
     * @param IRequest $request
     * @param FilterService $filterService
     */
    public function __construct(IRequest $request, FilterService $filterService)
    {
        $this->request = $request;
        $this->filterService = $filterService;
    }

    /**
     * @return array
     */
    public function getRestrictor()
    {
        $restrictor = [];

        $this->buildCategoryRestrictions($restrictor);
        $this->buildAccessibilityRestrictions($restrictor);
        $this->buildTypeRestrictions($restrictor);
        $this->buildCityRestrictions($restrictor);
        $this->buildCityPartRestrictions($restrictor);
        $this->buildRegionRestrictions($restrictor);
        $this->buildModifiedRestrictions($restrictor);

        return $restrictor;
    }

    /**
     * @param array $restrictor
     */
    protected function buildCategoryRestrictions(array &$restrictor)
    {
        $categories = $this->extractArrayValues(self::RESTRICTION_CATEGORY, $this->filterService->getCategoryValues());

        if ($categories) {
            $restrictor[] = $this->prepareCategoryRestrictions($categories);
        }
    }

    /**
     * @param array $restrictor
     */
    protected function buildAccessibilityRestrictions(array &$restrictor)
    {
        $accesibilityType = (string)
            $this->extractStringValue(self::RESTRICTION_ACCESSIBILITY_TYPE, $this->filterService->getAccessibilityTypes())
            ?: FilterService::ACCESSIBILITY_TYPE_DEFAULT;
        $accessibility = $this->extractArrayValues(self::RESTRICTION_ACCESSIBILITY, $this->filterService->getAccesibilityValues());

        if ($accessibility) {
            $restrictor[] = $this->prepareTypedAccessibilityRestrictions($accesibilityType, $accessibility);
        }
    }

    /**
     * @param array $restrictor
     */
    protected function buildTypeRestrictions(array &$restrictor)
    {
        $types = $this->extractArrayValues(self::RESTRICTION_TYPE, $this->filterService->getTypeValues());

        if ($types) {
            $restrictor[] = $this->prepareTypeRestrictions($types);
        }
    }

    /**
     * Pripravi restrikce pro mesto/obec.
     *
     * @param array $restrictor
     */
    protected function buildCityRestrictions(array &$restrictor)
    {
        $city = $this->extractStringValue(self::RESTRICTION_CITY);

        if ($city) {
            $restrictor[] = ["[city] = %s", $city];
        }
    }

    /**
     * Pripravi restrikce pro datum posledni modifikace.
     *
     * @param array $restrictor
     */
    protected function buildModifiedRestrictions(array &$restrictor)
    {
        $modified = $this->extractStringValue(self::RESTRICTION_MODIFIED);

        if ($modified) {
            $format = DateTime::RFC3339;
            $relaxedFormat = Strings::substring($format, 0, -1);

            $restriction = DateTime::createFromFormat($format, $modified) ?: DateTime::createFromFormat($relaxedFormat, $modified);

            if (false === $restriction) {
                throw new \MP\Module\Api\Exception\ApiException("Invalid valid for key '" . self::RESTRICTION_MODIFIED . "'. RFC3339 format expected.");
            }

            $restrictor[] = ["[modified_date] >= %t", $restriction];
        }
    }

    /**
     * Pripravi restrikce pro mestskou cast.
     *
     * @param array $restrictor
     */
    protected function buildCityPartRestrictions(array &$restrictor)
    {
        $cityPart = $this->extractStringValue(self::RESTRICTION_CITY_PART);

        if ($cityPart) {
            $restrictor[] = ["[city_part] = %s", $cityPart];
        }
    }

    /**
     * Pripravi restrikce pro region.
     *
     * @param array $restrictor
     */
    protected function buildRegionRestrictions(array &$restrictor)
    {
        $region = $this->extractStringValue(self::RESTRICTION_REGION);

        if ($region) {
            $restrictor[] = ["[region] = %s", $region];
        }
    }

    /**
     * Z requestu podle klice extrahuje hodnotu retezcove restrikce.
     * Zaroven provadi validaci, zda je hodnota skutecne retezec a volitelne kontroluje, zda je hodnota v povolenych.
     *
     * @param string $key
     * @param array $allowedValues
     *
     * @return string|null
     */
    protected function extractStringValue($key, array $allowedValues = [])
    {
        $value = $this->request->getQuery($key, null);

        if (null !== $value) {
            if (!Validators::is($value, 'string')) {
                throw new \MP\Module\Api\Exception\ApiException("Invalid value for key '{$key}'. String expected.");
            }

            if ($allowedValues && !in_array($value, $allowedValues)) {
                throw new \MP\Module\Api\Exception\ApiException("Invalid value for key '{$key}'. Allowed values are [" . implode(', ' , $allowedValues) . "].");
            }
        }

        return $value;
    }

    /**
     * Z requestu podle klice extrahuje hodnotu celociselne restrikce.
     * Zaroven provadi validaci, zda je hodnota skutecne cele cislo.
     *
     * @param string $key
     *
     * @return string|null
     */
    protected function extractIntegerValue($key)
    {
        $value = $this->request->getQuery($key, null);

        if (null !== $value) {
            if (!Validators::isNumericInt($value)) {
                throw new \MP\Module\Api\Exception\ApiException("Invalid value for key '{$key}'. Numeric integer expected.");
            }
        }

        return $value;
    }

    /**
     * Z requestu podle klice extrahuje hodnoty restrikce pro pole.
     * Zaroven provadi validaci, zda je hodnota v povolenych hodnotach.
     *
     * @param string $key
     * @param array $allowedValues
     *
     * @return array
     */
    protected function extractArrayValues($key, array $allowedValues)
    {
        $restrictorValues = [];

        $allowedValues = array_flip($allowedValues);

        $values = $this->request->getQuery($key, []);

        if (!is_array($values)) {
            throw new \MP\Module\Api\Exception\ApiException("Invalid value for key '{$key}'. Array expected.");
        }

        array_walk($values, function($value) use (&$restrictorValues, $key, $allowedValues) {
            if (isset($allowedValues[$value])) {
                $restrictorValues[] = $allowedValues[$value];
            } else {
                throw new \MP\Module\Api\Exception\ApiException("Invalid value for key '{$key}'. Allowed values are [" . implode(', ' , array_keys($allowedValues)) . "].");
            }
        });

        return $restrictorValues;
    }
}
