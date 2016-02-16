<?php

namespace MP\Manager\Resolver;

use MP\Manager\ManagerFactory;
use MP\Mapper\IMapper;
use MP\Util\Arrays;
use MP\Util\Strings;
use Nette\Caching\Cache;
use Nette\Utils\Validators;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractEnumValueResolver implements IEnumValueResolver
{
    /** @var ManagerFactory */
    protected $managerFactory;

    /** @var Cache */
    protected $cache;

    /**
     * @param ManagerFactory $managerFactory
     * @param Cache $cache
     */
    public function __construct(ManagerFactory $managerFactory, Cache $cache)
    {
        $this->managerFactory = $managerFactory;
        $this->cache = $cache;
    }

    /**
     * Vrati mapovani sloupce s cizim klicem do ciselniku na jeho navazanou tabulku.
     *
     * @return array
     */
    abstract protected function getEnumColumnTableMapping();

    /**
     * Dohledava hodnotu ciselniku pro data nactena z DB
     * @param array $values
     * @return array
     */
    public function findResolve(array $values)
    {
        $resolvedValues = [];

        $columns = $this->getEnumColumnTableMapping();

        foreach ($columns as $column => $table) {
            if (!array_key_exists($column, $values)) {
                $suffixedColumn = $this->getColumnSuffixedName($column);

                if (array_key_exists($suffixedColumn, $values)) {
                    $value = $values[$suffixedColumn];
                    $resolvedValues[$column] = ($value ? $this->resolveValue($table, $column, $value) : null);
                }
            }
        }

        $resolvedValues = array_merge($values, $resolvedValues);

        return $resolvedValues;
    }

    /**
     * Slouzi pro premapovani ciselnikovych hodnot na _id v pripade persistovani
     * @param array $values
     * @return array
     */
    public function persistResolve(array $values)
    {
        $resolvedValues = [];

        $columns = $this->getEnumColumnTableMapping();

        foreach ($columns as $column => $table) {
            if (array_key_exists($column, $values)) {
                $value = $values[$column];
                $resolvedValues[$this->getColumnSuffixedName($column)] = ($value ? $this->resolveId($table, $column, $value) : null);

                unset($values[$column]);
            }
        }

        $resolvedValues = array_merge($values, $resolvedValues);

        return $resolvedValues;
    }

    /**
     * Normalizuje hodnoty ciselniku. Premapuje pair_key na title.
     *
     * @param array $values
     *
     * @return array
     */
    public function normalize(array $values)
    {
        $normalizedValues = [];

        $columns = $this->getEnumColumnTableMapping();

        $columns = array_combine(
            array_map(Strings::class . '::toCamelCase', array_keys($columns)),
            array_values($columns)
        );

        foreach ($columns as $column => $table) {
            if (array_key_exists($column, $values)) {
                $normalizedValues[$column] = ($values[$column] ? $this->normalizeValue($table, $values[$column]) : null);
            }
        }

        $normalizedValues = array_merge($values, $normalizedValues);

        return $normalizedValues;
    }

    /**
     * Dohleda hodnotu z ciselniku pro ID.
     *
     * @param string $table
     * @param string $column
     * @param int $value
     *
     * @return string
     */
    protected function resolveValue($table, $column, $value)
    {
        if (!Validators::is($value, 'scalar')) {
            throw new \Nette\InvalidArgumentException("Non-scalar ID key could not be resolved.");
        }

        $values = $this->cache->load("{$table}ResolvedValues", function() use ($table, $value, $column) {
            $manager = $this->managerFactory->create($table);

            $results = $manager->findAll() ?: [];
            return $this->getEnumTitles($results, $column);
        });

        $result = Arrays::get($values, $value, null);

        if (null === $result) {
            throw new \Nette\InvalidArgumentException("Unknown enum value for column '{$column}' and ID/pair key '{$value}' in table '{$table}'.");
        }

        return $result;
    }

    /**
     * Dohleda hodnotu z ciselniku pro ID.
     *
     * @param string $table
     * @param int|string $value
     *
     * @return string
     */
    protected function normalizeValue($table, $value)
    {
        if (!Validators::is($value, 'scalar')) {
            throw new \Nette\InvalidArgumentException("Non-scalar pair key could not be resolved.");
        }

        $values = $this->cache->load("{$table}NormalizedValues", function() use ($table, $value) {
            $manager = $this->managerFactory->create($table);

            $result = $manager->findAll() ?: [];
            $result = Arrays::pairs($result, 'pair_key', 'title');

            return $result;
        });

        $result = Arrays::get($values, $value, $value);

        return $result;
    }

    /**
     * Dohleda ID z ciselniku pro hodnotu.
     *
     * @param string $table
     * @param string $column
     * @param string $value
     *
     * @return int
     */
    protected function resolveId($table, $column, $value)
    {
        if (!Validators::is($value, 'scalar')) {
            throw new \Nette\InvalidArgumentException("Non-scalar value could not be resolved.");
        }

        $values = $this->cache->load("{$table}ResolvedIds", function() use ($table, $value) {
            $manager = $this->managerFactory->create($table);

            $result = $manager->findAll() ?: [];
            $result = Arrays::pairs($result, 'title', 'id');

            return $result;
        });

        $result = Arrays::get($values, $value, null);

        if (null === $result) {
            throw new \Nette\InvalidArgumentException("Unknown enum ID for column '{$column}' and value '{$value}' in table '{$table}'.");
        }

        return $result;
    }

    /**
     * Vrati nazev sloupce v zakladnim tvaru, tzn. bez suffixu.
     *
     * @param string $column
     *
     * @return string
     */
    protected function getColumnBaseName($column)
    {
        if (Strings::endsWith($column, IEnumValueResolver::KEY_SUFFIX)) {
            $column = Strings::substring($column, 0, -Strings::length(IEnumValueResolver::KEY_SUFFIX));
        }

        return $column;
    }

    /**
     * Vrati nazev sloupce v tvaru se suffixem.
     *
     * @param string $column
     *
     * @return string
     */
    private function getColumnSuffixedName($column)
    {
        $column .= IEnumValueResolver::KEY_SUFFIX;

        return $column;
    }

    /**
     * Z vysledku utvori dvojice id => zobrazovana hodnota ciselniku
     * @param array|null $results
     * @param string $column
     *
     * @return array
     */
    protected function getEnumTitles($results, $column)
    {
        return Arrays::pairs($results, IMapper::ID, 'title');
    }
}
