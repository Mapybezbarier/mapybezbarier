<?php

namespace MP\Exchange\Service;

use MP\Util\Arrays;
use Nette\Utils\Json;

/**
 * Sluzba pro poskytnuti metadat importovanych zaznamu.
 *
 * Metadata ziskava z JsonSchema.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ExchangeMetadata
{
    /** @const Nazev souboru s validacnim schematem */
    const SCHEMA = 'schema.jsonschema';

    /** @const Datove typy */
    const TYPE_ARRAY = 'array',
        TYPE_ENUM = 'enum',
        TYPE_INTEGER = 'integer',
        TYPE_NUMBER = 'number',
        TYPE_STRING = 'string',
        TYPE_DATE_TIME = 'datetime',
        TYPE_BOOLEAN = 'boolean';

    /** @const Specifikace formatu */
    const FORMAT_DATE_TIME = 'DATE_TIME';

    /** @const Parametry pravidel */
    const RULE_TYPE = 'type',
        RULE_RULES = 'rules',
        RULE_ENUM = 'enum',
        RULE_MINIMUM = 'minimum',
        RULE_MAXIMUM = 'maximum',
        RULE_FORMAT = 'format',
        RULE_REQUIRED = 'required';

    /** @var array */
    protected $schema = [];

    /** @var array */
    protected $rules = [];

    /** @var array */
    protected $requirements = [];

    /** @var array */
    protected $properties = [];

    /**
     * QualityValidator constructor.
     */
    public function __construct()
    {
        $this->schema = $this->prepareSchema();
        $this->rules = $this->prepareRules($this->schema);
        $this->requirements = $this->prepareRequirements($this->rules);
        $this->properties = $this->prepareProperties($this->rules);
    }

    /**
     * Vrati vyzadovane klice.
     *
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Vrati property.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Vrati validacni pravidlo pro klic.
     *
     * @return array
     */
    public function getRule()
    {
        $keys = func_get_args();
        $keys = array_filter($keys);

        $rule = Arrays::get($this->rules, $keys, []);

        return $rule;
    }

    /**
     * Vrati datovy typ hodnoty pro klic.
     *
     * @return string
     */
    public function getType()
    {
        $keys = func_get_args();
        $keys = array_filter($keys);

        $rule = Arrays::get($this->rules, $keys, []);

        if (self::FORMAT_DATE_TIME === Arrays::get($rule, self::RULE_FORMAT, false)) {
            $type = self::TYPE_DATE_TIME;
        } else {
            $type = Arrays::get($rule, self::RULE_TYPE, self::TYPE_ARRAY);
        }

        return $type;
    }

    /**
     * Pripravi schema.
     *
     * @return array
     */
    protected function prepareSchema()
    {
        $file = ASSET_DIR . "/schema/" . self::SCHEMA;

        if (file_exists($file)) {
            $schema = file_get_contents($file);

            try {
                $schema = Json::decode($schema, Json::FORCE_ARRAY);
            } catch (\Nette\Utils\JsonException $e) {
                throw new \Nette\InvalidStateException($e->getMessage());
            }

            $schema = Arrays::get($schema, ['properties', 'object', 'items', 'properties'], []);
        } else {
            throw new \Nette\InvalidStateException("Validator schema '{$file}' missing.");
        }

        return $schema;
    }

    /**
     * Z Json Schema pripravi pravidla pro validaci.
     *
     * @param array $schema
     *
     * @return array
     */
    protected function prepareRules(array $schema)
    {
        $rules = [];

        foreach ($schema as $key => $attributes) {
            if (self::TYPE_ARRAY !== $attributes[self::RULE_TYPE]) {
                $rules[$key] = $attributes;
            } else {
                $rules[$key] = $this->prepareRules(Arrays::get($attributes, ['items', 'properties'], []));
            }
        }

        return $rules;
    }

    /**
     * Pripravi vyzadovane sloupce.
     *
     * @param array $rules
     * @param string|null $namespace
     *
     * @return array
     */
    protected function prepareRequirements(array $rules, $namespace = null)
    {
        $requirements = [];

        foreach ($rules as $key => $attributes) {
            $type = $this->getType($namespace, $key);

            if (self::TYPE_ARRAY !== $type) {
                $required = Arrays::get($attributes, self::RULE_REQUIRED, false);

                if ($required) {
                    $requirements[$key] = true;
                }
            } else {
                $requirements[$key] = $this->prepareRequirements($attributes, $key);
            }
        }

        return $requirements;
    }

    /**
     * Pripravi property.
     *
     * @param array $rules
     * @param string|null $namespace
     *
     * @return array
     */
    protected function prepareProperties(array $rules, $namespace = null)
    {
        $properties = [];

        foreach ($rules as $key => $attributes) {
            $type = $this->getType($namespace, $key);

            if (self::TYPE_ARRAY !== $type) {
                $properties[] = $key;
            } else {
                $properties[$key] = $this->prepareProperties($attributes, $key);
            }
        }

        return $properties;
    }
}
