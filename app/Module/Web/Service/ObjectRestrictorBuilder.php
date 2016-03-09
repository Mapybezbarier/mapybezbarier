<?php

namespace MP\Module\Web\Service;

use MP\Service\FilterService;
use MP\Util\Arrays;
use Nette\Http\Request;
use Nette\Http\Session;
use Nette\Utils\Validators;

/**
 * Sestavi restriktor pro vypis objektu v mape.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectRestrictorBuilder extends \MP\Service\ObjectRestrictorBuilder
{
    /** @const Nazev sekce v session. */
    const SECTION = 'filter';

    /** @var Request */
    protected $request;

    /** @var Session */
    protected $session;

    /** @var FilterService */
    protected $filterService;

    /**
     * @param Request $request
     * @param Session $session
     * @param FilterService $filterService
     */
    public function __construct(Request $request, Session $session, FilterService $filterService)
    {
        $this->request = $request;
        $this->session = $session;
        $this->filterService = $filterService;

        $this->init();
    }

    /**
     * Inicializace tridy.
     */
    protected function init()
    {
        $values = $this->request->getQuery();

        $this->prepareRestrictions($values);
    }

    /**
     * @return array|null
     */
    public function getRestrictor()
    {
        $restrictor = [];

        if ($accessibility = $this->getAccesibility()) {
            $restrictor[] = $this->prepareAccessibilityRestrictions($accessibility);
        }

        if ($categories = $this->getCategories()) {
            $restrictor[] = $this->prepareCategoryRestrictions($categories);
        }

        if ($types = $this->getTypes()) {
            $restrictor[] = $this->prepareTypeRestrictions($types);
        } else {
            $restrictor[] = ['%or', [$this->getCertifiedRestriction(), $this->getOutdatedRestriction()]];
        }

        $restrictor[] = ["[latitude] IS NOT NULL"];
        $restrictor[] = ["[longitude] IS NOT NULL"];

        $restrictor = array_filter($restrictor);

        return $restrictor ?: null;
    }

    /**
     * Ziska aktualni hodnoty filtru
     * Pro moznost exportu a vnorene mapy
     *
     * @param bool $apiFormat chci format pro API (misto ciselnych klicu chci hodnoty)
     *
     * @return array
     */
    public function getActiveQueryData($apiFormat = false)
    {
        $ret = [];

        if ($accessibility = $this->getAccesibility()) {
            if ($apiFormat) {
                $allValues = $this->filterService->getAccesibilityValues();
                $accessibilityCodes = array_intersect_key($allValues, array_flip($accessibility));
                $ret[self::RESTRICTION_ACCESSIBILITY] = array_values($accessibilityCodes);
            } else {
                $ret[self::RESTRICTION_ACCESSIBILITY] = array_values($accessibility);
            }
        }

        if ($types = $this->getTypes()) {
            $ret[self::RESTRICTION_TYPE] = array_values($types);
        }

        if ($categories = $this->getCategories()) {
            if ($apiFormat) {
                $allValues = $this->filterService->getCategoryValues();
                $categoriesCodes = array_intersect_key($allValues, array_flip($categories));
                $ret[self::RESTRICTION_CATEGORY] = array_values($categoriesCodes);
            } else {
                $ret[self::RESTRICTION_CATEGORY] = array_values($categories);
            }
        }

        return $ret;
    }

    /**
     * Vraci aktualni nastaveni filtru - prisutpnost
     *
     * @return array|null
     */
    public function getAccesibility()
    {
        return $this->session->getSection(self::SECTION)->{self::RESTRICTION_ACCESSIBILITY};
    }

    /**
     * Vraci aktualni nastaveni filtru - kategorie
     *
     * @return array|null
     */
    public function getCategories()
    {
        return $categories = $this->session->getSection(self::SECTION)->{self::RESTRICTION_CATEGORY};
    }

    /**
     * Vraci aktualni nastaveni filtru - typy podkladu
     *
     * @return array
     */
    public function getTypes()
    {
        $types = $this->session->getSection(self::SECTION)->{self::RESTRICTION_TYPE};

        if (!$types) {
            $types = [
                FilterService::TYPE_CERTIFIED,
                FilterService::TYPE_OUTDATED,
            ];
        }

        $types = array_combine($types, $types);

        return $types;
    }

    /**
     * Pripravi restrikce na zaklade hodnot.
     *
     * @param array $restrictions
     * @param bool $override
     */
    public function prepareRestrictions(array $restrictions, $override = false)
    {
        $section = $this->session->getSection(self::SECTION);

        $accessibility = $this->getRestrictionValues($restrictions, self::RESTRICTION_ACCESSIBILITY);

        if ($accessibility || $override) {
            $section->{self::RESTRICTION_ACCESSIBILITY} = $accessibility;
        }

        $categories = $this->getRestrictionValues($restrictions, self::RESTRICTION_CATEGORY);

        if ($categories || $override) {
            $section->{self::RESTRICTION_CATEGORY} = $categories;
        }

        $types = $this->getRestrictionValues($restrictions, self::RESTRICTION_TYPE, function ($value) {
            return Validators::is($value, 'string');
        });

        if ($types) {
            $section->{self::RESTRICTION_TYPE} = $types;
        } else if ($override) {
            $section->{self::RESTRICTION_TYPE} = [FilterService::TYPE_CERTIFIED, FilterService::TYPE_OUTDATED];
        }
    }

    /**
     * @param array $values
     * @param string $restriction
     * @param callable $filter
     *
     * @return mixed
     */
    protected function getRestrictionValues(array $values, $restriction, callable $filter = null)
    {
        $values = Arrays::get($values, $restriction, null);

        if (is_array($values)) {
            $values = array_filter($values, $filter ?: $this->getDefaultValuesFilter());
        }

        return $values;
    }

    /**
     * @return Callback
     */
    protected function getDefaultValuesFilter()
    {
        return callback(Validators::class, 'isNumericInt');
    }
}
