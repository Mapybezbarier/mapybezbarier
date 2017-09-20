<?php

namespace MP\Mapper;
use MP\Util\Strings;

/**
 * Mapper pro tabulku s RUIAN adresami
 */
class RuianMapper extends DatabaseMapper
{
    /**
     * Vlozi vice zaznamu jednim dotazem
     * @param array $rows
     */
    public function insertAddresses($rows)
    {
        $query = ["INSERT INTO %n", $this->table, ' %ex ', $rows];

        $this->executeQuery($query);
    }

    /**
     * Nastavi constraint na PK na DEFERRED - vyhodnoceni az pri commitu
     * Smaze indexy
     * Smaze data
     */
    public function prepareImport()
    {
        $this->executeQuery("
            SET CONSTRAINTS [map_object_ruian_address_fk] DEFERRED;
            SET CONSTRAINTS [versions].[map_object_ruian_address_fk] DEFERRED;
            DROP INDEX [ruian_index_1];
            DROP INDEX [ruian_index_2];
        ");
        $this->delete([]);
    }

    /**
     * Znovuvytvori indexy
     * Prirpavi tabulku ruian_city pro rychlejsi naseptavani
     */
    public function finishImport()
    {
        $this->executeQuery("
            CREATE INDEX [ruian_index_1]
            ON [ruian]
            USING btree
            ([zipcode], [city_part], [street_desc_no]);
            CREATE INDEX [ruian_index_2]
            ON [ruian]
            USING btree
            ([city]);
        ");

        $this->executeQuery("
            DELETE FROM [ruian_city];
            INSERT INTO [ruian_city]
                SELECT DISTINCT [zipcode], [city], [city_part], NULL, NULL FROM [ruian];
            UPDATE [ruian_city] SET
                [search_city] = remove_diacritics([city]),
                [search_city_part] = remove_diacritics([city_part])
            ;
        ");
    }

    /**
     * Dohleda ID zaznamu dle kriterii objektu (v camelCase notaci)
     * @param array $criteria
     * @return \Dibi\Row[]|null
     */
    public function selectIdsByObjectCriteria($criteria)
    {
        $restrictor = [];

        foreach ($criteria as $key => $value) {
            $key = Strings::toUnderscore($key);

            if (in_array($key, ['id', 'street_desc_no', 'street_orient_no'], true)) {
                $restrictor[] = ["[$key] = %iN", $value];
            } else {
                $restrictor[] = ["[$key] = %sN", $value];
            }
        }

        $query = ["SELECT [id] FROM %n", $this->table];
        $query[] = "WHERE %and";
        $query[] = $restrictor;

        return $this->executeQuery($query)->fetchAll();
    }

    /**
     * Dohleda kombinace PCS, obec, cast obce
     * Kvuli vykonnosti v oddelene tabulce
     * @param string $term
     * @return \Dibi\Row[]|null
     */
    public function selectZipcodeCityCitypart($term)
    {
        $restrictor = [
            ["[zipcode] LIKE '{$term}%'"],
            ["[search_city] ILIKE '%' || remove_diacritics('{$term}') || '%'"],
            ["[search_city_part] ILIKE '%' || remove_diacritics('{$term}') || '%'"],
        ];

        $query = ['SELECT DISTINCT [zipcode], [city], [city_part] FROM ruian_city'];
        $query[] = 'WHERE %or';
        $query[] = $restrictor;
        $query[] = 'ORDER BY [zipcode]';

        return $this->executeQuery($query)->fetchAll();
    }

    /**
     * Vrati, zda ma dana cast obce ulice
     *
     * @param string $zipcode
     * @param string|string[] $city
     * @param string|string[] $cityPart
     *
     * @return bool
     */
    public function hasStreet($zipcode, $city, $cityPart): bool
    {
        $restrictor = [
            ['[street] IS NOT NULL'],
            ['[zipcode] = %s', $zipcode],
        ];

        $restrictor[] = is_array($city) ? ['[city] IN %in', $city] : ['[city] = %s', $city];
        $restrictor[] = is_array($cityPart) ? ['[city_part] IN %in', $cityPart] : ['[city_part] = %s', $cityPart];

        $query = ['SELECT COUNT(*) > 0 FROM %n', $this->table];
        $query[] = 'WHERE %and';
        $query[] = $restrictor;

        return (bool) $this->executeQuery($query)->fetchSingle();
    }

    /**
     * Dohleda mozne ulice pro danou cast obce
     *
     * @param string $term
     * @param string $zipcode
     * @param string|string[] $city
     * @param string|string[] $cityPart
     *
     * @return \Dibi\Row[]|null
     */
    public function findStreet($term, $zipcode, $city, $cityPart)
    {
        $restrictor = [
            ["remove_diacritics([street]) ILIKE '%' || remove_diacritics('{$term}') || '%'"],
            ['[zipcode] = %s', $zipcode],
        ];

        $restrictor[] = is_array($city) ? ['[city] IN %in', $city] : ['[city] = %s', $city];
        $restrictor[] = is_array($cityPart) ? ['[city_part] IN %in', $cityPart] : ['[city_part] = %s', $cityPart];

        $query = ['SELECT DISTINCT [street], [zipcode], [city], [city_part] FROM %n', $this->table];
        $query[] = 'WHERE %and';
        $query[] = $restrictor;
        $query[] = 'ORDER BY [street], [zipcode], [city], [city_part]';

        return $this->executeQuery($query)->fetchAll();
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
        $restrictor = [
            ['[zipcode] = %s', $zipcode],
            ['[city] = %s', $city],
            ['[city_part] = %s', $cityPart],
            [
                '%or',
                [
                    ["[street_desc_no]::text LIKE '{$term}%'"],
                    ["[street_orient_no]::text LIKE '{$term}%'"]
                ]
            ]
        ];

        if ($street) {
            $restrictor[] = ['[street] = %s', $street];
        };

        $query = ['SELECT [id], [street_desc_no], [street_orient_no], [street_orient_symbol] FROM %n', $this->table];
        $query[] = 'WHERE %and';
        $query[] = $restrictor;
        $query[] = 'ORDER BY [street_desc_no], [street_orient_no]';

        return $this->executeQuery($query)->fetchAll();
    }
}
