<?php

namespace MP\Mapper;

/**
 * Databazovy mapper pro jazykove zavisle zaznamy, tj. zaznamy, ktere maji jazykove nezavisla data v hlavni tabulkce a
 * jazykove zavisla v tabulce jine.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractLangAwareDatabaseMapper extends DatabaseMapper
{
    /** @const Nazev sloupce s jazykem. */
    const LANG_COLUMN = 'lang_id';

    /** @const Suffix jazykove tabulky. */
    const LANG_TABLE_SUFFIX = '_lang';

    /** @var string Nazev jazykove tabulky, typicky konci suffixem _lang. */
    protected $langTable;

    /**
     * Vrati nazev sloupce, pres ktery je zaznam z jazykove tabulky navazan na zaznam z datove tabulky.
     *
     * @return string
     */
    abstract protected function getDataKeyColumn();

    /**
     * Vrati jazykove zavisle sloupce.
     *
     * @return string[]
     */
    abstract protected function getLangAwareColumns();

    /**
     * @override Nastaveni datove a jazykove tabulky.
     *
     * @param string $table
     */
    public function setTable($table)
    {
        parent::setTable($table);

        $this->langTable = $table . self::LANG_TABLE_SUFFIX;
    }

    /**
     * @override Cast dat zaznamu jsou v datove, cast je v jazykove tabulce.
     *
     * @param array $values
     *
     * @return int
     */
    public function insert(array $values)
    {
        $this->transaction->begin();

        list($independentValues, $dependentValues) = $this->prepareValues($values);

        $id = parent::insert($independentValues);

        if ($dependentValues) {
            $this->insertLangData($id, $this->lang->getLang(), $dependentValues);
        }

        $this->transaction->commit();

        return $id;
    }

    /**
     * @override Cast dat zaznamu jsou v datove, cast je v jazykove tabulce.
     *
     * @param array $values
     * @param array $restrictor
     * @param string|null $returning
     *
     * @return \Dibi\Result|int
     */
    public function update(array $values, array $restrictor, $returning = null)
    {
        $this->transaction->begin();

        list($independentValues, $dependentValues) = $this->prepareValues($values);

        $id = parent::update($independentValues, $restrictor, IMapper::ID);

        if ($dependentValues) {
            $this->updateLangData($id->fetchSingle(), $this->lang->getLang(), $dependentValues);
        }

        $this->transaction->commit();

        return $id;
    }

    /**
     * Pripravi hodnoty pro insert/update.
     *
     * Rozdeluji hodnoty na 2 mnoziny podle jazykove zavislosti.
     *
     * @param array $values
     *
     * @return array
     */
    protected function prepareValues(array $values)
    {
        $dependentColumns = array_flip($this->getLangAwareColumns());

        $independent = $dependent = [];

        foreach ($values as $key => $value) {
            if (!isset($dependentColumns[$key])) {
                $independent[$key] = $value;
            } else {
                $dependent[$key] = $value;
            }
        }

        return [$independent, $dependent];
    }

    /**
     * @param int $id
     * @param string $lang
     * @param array $values
     *
     * @throws \Dibi\Exception
     */
    public function saveLangData($id, $lang, array $values)
    {
        $restrictor = [
            ["[{$this->getDataKeyColumn()}] = %i", $id],
            ["[" . self::LANG_COLUMN . "] = %s", $lang],
        ];

        $query = ["SELECT COUNT(*) FROM %n", $this->langTable];
        $query = $this->buildWhere($restrictor, $query);

        $exists = (bool) $this->executeQuery($query)->fetchSingle();

        if (false === $exists) {
            $this->insertLangData($id, $lang, $values);
        } else {
            $this->updateLangData($id, $lang, $values);
        }
    }

    /**
     * Vlozi jazykova data zaznamu.
     *
     * @param int $id
     * @param string $lang
     * @param array $values
     *
     * @throws \Dibi\Exception
     */
    protected function insertLangData($id, $lang, array $values)
    {
        $values[$this->getDataKeyColumn()] = $id;
        $values[self::LANG_COLUMN] = $lang;

        $query = ["INSERT INTO %n", $this->langTable, $values];

        $this->executeQuery($query);
    }

    /**
     * Zaaktualizuje jazykova data zaznamu.
     *
     * @param int $id
     * @param string $lang
     * @param array $values
     *
     * @throws \Dibi\Exception
     */
    protected function updateLangData($id, $lang, array $values)
    {
        $restrictor = [
            ["[{$this->getDataKeyColumn()}] = %i", $id],
            ["[" . self::LANG_COLUMN . "] = %s", $lang],
        ];

        $query = ["UPDATE %n", $this->langTable, "SET", $values];
        $query = $this->buildWhere($restrictor, $query);

        $this->executeQuery($query);
    }

    /**
     * @override jazykove zavisla data hleda primarne pro aktualni jazyk
     *
     * @param string[] $query
     *
     * @return string[]
     */
    protected function buildSelect(&$query)
    {
        if ($this->context->mergeLanguageData()) {
            $this->buildMergedSelect($query);
        } else {
            $this->buildJoinedSelect($query);
        }
    }

    /**
     * @param string[] $query
     *
     * @return array
     */
    private function buildMergedSelect(&$query)
    {
        $parts = explode('.' ,$this->table);

        $alias = end($parts) . '_id';

        $query = [
            "
                SELECT *
                FROM %n", $this->table, "
                LEFT JOIN (
                    SELECT DISTINCT ON (", $alias, ") *
                    FROM %n", $this->langTable, "
                    ORDER BY", $alias, ", lang_id = %s", $this->lang->getLang(), " DESC
                ) lang ON (%n", $this->table, ".id = lang.", $alias, ")
            "
        ];

        return $query;
    }

    /**
     * @param string[] $query
     *
     * @return array
     */
    private function buildJoinedSelect(&$query)
    {
        $query = [
            "
                SELECT *
                FROM [{$this->table}]
                LEFT JOIN [{$this->langTable}] ON (
                    [{$this->langTable}].[{$this->getDataKeyColumn()}] = [{$this->table}].[id]
                    AND [{$this->langTable}].[lang_id] = %s", $this->lang->getLang(), "
                )
            "
        ];

        return $query;
    }
}
