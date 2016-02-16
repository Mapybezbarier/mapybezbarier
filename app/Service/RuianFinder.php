<?php

namespace MP\Exchange\Service;
use MP\Mapper\RuianMapper;

/**
 * Sluzba pro dohledani RUIAN ID.
 */
class RuianFinder
{
    /**
     * @param RuianMapper $mapper
     */
    public function __construct(RuianMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Strom pro dohledani ruian ID
     * Na kazde urovni postupuji po vsech moznostech:
     *  pokud najdu prave 1 vyhovujici zaznam, koncim
     *  pokud najdu N vyhovujicich zaznamu, pokracuji na dalsi moznosti stejne urovne
     *     pokud je na cele urovni jen N, koncim s notice
     *  pokud najdu 0 vyhovujicich zaznamu, prochazim rekurzivne podstrom
     */
    protected $findTree = [
        'L1' => [
            'parts' => ['zipcode', 'cityPart', 'streetDescNo'],
            'levels' => [
                'L11' => [
                    'parts' => ['zipcode', 'city', 'street', 'streetDescNo'],
                    'levels' => [
                        'L111' => [
                            'parts' => ['zipcode', 'city', 'streetDescNo'],
                        ],
                        'L112' => [
                            'parts' => ['zipcode', 'street', 'streetDescNo'],
                        ],
                    ]
                ],
                'L12' => [
                    'parts' => ['city', 'cityPart', 'street', 'streetDescNo'],
                    'levels' => [
                        'L121' => [
                            'parts' => ['city', 'street', 'streetDescNo'],
                        ],
                        'L122' => [
                            'parts' => ['city', 'cityPart', 'streetDescNo'],
                        ],
                    ],
                ],
            ],
        ],
        'L2' => [
            'parts' => ['zipcode', 'cityPart', 'streetDescNo', 'streetOrientNo'],
            'levels' => [
                'L21' => [
                    'parts' => ['zipcode', 'city', 'street', 'streetDescNo', 'streetOrientNo'],
                ],
            ],
        ],
    ];

    /**
     * Dohleda RUIAN ID na zaklade informaci o objektu.
     *
     * @param array $object
     * @return int|null
     */
    public function find(array $object)
    {
        $ret = null;

        // pokud je RUIAN ID vyplneno, overim jeho existenci
        if (!empty($object['ruianAddress'])) {
            $ret = $this->mapper->selectIdsByObjectCriteria(['id' => $object['ruianAddress']]);

            if ($ret) {
                $ret = $object['ruianAddress'];
            } else {
                ImportLogger::addError($object, 'unknownRuian');
            }
        } else if (
            (!empty($object['zipcode']) || !empty($object['cityPart']))
            && !empty($object['streetDescNo']))
        {
            $duplicity = false;
            $ret = $this->traverseTree($object, $this->findTree, $duplicity);

            if ($duplicity) {
                ImportLogger::addNotice($object, 'ruianDuplicity');
            } else if (!$ret) {
                ImportLogger::addNotice($object, 'ruianNotFound');
            }
        }

        return $ret;
    }

    /**
     * Rekurzivne prohledavam strom
     * @param array $object
     * @param array $levels
     * @param bool $duplicity nalezena nejaka duplicita?
     * @return int|null ruian ID
     */
    protected function traverseTree($object, $levels, &$duplicity)
    {
        $ret = null;

        foreach ($levels as $level) {
            $results = $this->findByObjectParts($object, $level['parts']);
            $count = count($results);

            if ($count == 1) { // mam vysledek, koncim
                $ret = $results[0]['id'];
                $duplicity = false;
            } else if ($count > 1) { // mam vice vysledku, zkusim dalsi polozky urovne
                $duplicity = true;
                continue;
            } else if ($count < 1 && !empty($level['levels'])) { // nemam zadne vysledky, zkusim podstrom
                $ret = $this->traverseTree($object, $level['levels'], $duplicity);
            }

            if ($ret) {
                break;
            }
        }

        return $ret;
    }

    /**
     * Dohledam vsechny RUIAN ID podle presne shody
     * @param array $object
     * @param array $parts seznam sloupcu, dle kterych hledam
     * @return array
     */
    protected function findByObjectParts($object, $parts)
    {
        $ret = null;
        $criteria = [];
        
        foreach ($parts as $part) {
            if (!empty($object[$part])) {
                $criteria[$part] = $object[$part];
            }
        }

        // abych hledal, musi mit objekt vse potrebne nastaveno
        if (count($parts) === count($criteria)) {
            $ret = $this->mapper->selectIdsByObjectCriteria($criteria);
        }
        
        return $ret;
    }

    /**
     * Dohleda kombinace PCS, obec, cast obce
     * @param string $term
     * @return \Dibi\Row[]|null
     */
    public function findZipcodeCityCitypart($term)
    {
        return $this->mapper->selectZipcodeCityCitypart($term);
    }

    /**
     * Dohleda mozne ulice pro danou cast obce
     *
     * @param string $term
     * @param string $zipcode
     * @param string $city
     * @param string $cityPart
     *
     * @return \Dibi\Row[]|null
     */
    public function findStreet($term, $zipcode, $city, $cityPart)
    {
        return $this->mapper->findStreet($term, $zipcode, $city, $cityPart);
    }

    /**
     * Dohleda mozna cisla domu pro danou ulici
     *
     * @param string $term
     * @param string $zipcode
     * @param string $city
     * @param string $cityPart
     * @param string $street
     *
     * @return \Dibi\Row[]|null
     */
    public function findStreetNumber($term, $zipcode, $city, $cityPart, $street)
    {
        return $this->mapper->findStreetNumber($term, $zipcode, $city, $cityPart, $street);
    }
}
