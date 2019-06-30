<?php

namespace MP\Exchange\Validator;

use MP\Exchange\Service\ExchangeMetadata;
use MP\Exchange\Service\ImportLogger;
use Nette\Utils\Json;
use Nette\Utils\Validators;
use Tracy\Dumper;

/**
 * Validator kvality objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class QualityValidator implements IValidator
{
    /** @const Klice objektu se specialni validaci */
    const KEY_EXTENAL_DATA = 'externalData',
        KEY_RUIAN_ADDRESS = 'ruianAddress',
        KEY_LATITUDE = 'latitude',
        KEY_LONGITUDE = 'longitude';

    /** @var ExchangeMetadata */
    protected $metadata;

    /** @var array */
    protected $object = [];

    /**
     * @param ExchangeMetadata $metadata
     */
    public function __construct(ExchangeMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @param array $object
     */
    public function validate(array $object)
    {
        $this->object = $object;

        $this->validateRequirements($object, $this->metadata->getRequirements());

        $this->validateValues($object);
        $this->validateLocation($object);
        $this->validateExternalData($object);
    }

    /**
     * @param array $values
     * @param array $requirements
     */
    protected function validateRequirements(array $values, array $requirements)
    {
        foreach ($requirements as $key => $requirement) {
            if (!is_array($requirement)) {
                if (!isset($values[$key])) {
                    $this->addError('missingObjectKey', ['key' => $key]);
                }
            } else if (isset($values[$key])) {
                $this->validateRequirements($values[$key], $requirement);
            }
        }
    }

    /**
     * @param array $values
     * @param string|null $namespace
     */
    protected function validateValues(array $values, $namespace = null)
    {
        foreach ($values as $key => $value) {
            $rule = $this->metadata->getRule($namespace, $key);

            if ($rule) {
                $type = $this->metadata->getType($namespace, $key);

                if (ExchangeMetadata::TYPE_ARRAY !== $type) {
                    if (null !== $value) {
                        $this->validateType($key, $value, $type);
                        $this->validateEnumValue($key, $value, $rule);
                        $this->validateExtremeValue($key, $value, $rule);
                    }
                } else {
                    foreach ($value as $item) {
                        $this->validateValues($item, $key);
                    }
                }
            } else {
                $this->addError('unknownObjectKey', ['key' => $key]);
            }
        }
    }

    /**
     * Overi existenci polohy objektu.
     *
     * @param array $object
     */
    protected function validateLocation(array $object)
    {
        if (empty($object[self::KEY_RUIAN_ADDRESS]) && (empty($object[self::KEY_LATITUDE]) || empty($object[self::KEY_LONGITUDE]))) {
            $this->addError('unknownObjectLocation');
        }
    }

    /**
     * Overi externi data objektu.
     *
     * @param array $object
     */
    protected function validateExternalData(array $object)
    {
        if (!empty($object[self::KEY_EXTENAL_DATA])) {
            if (!$this->validateJson($object[self::KEY_EXTENAL_DATA])) {
                $this->addError('invalidObjectExternalData');
            }
        }
    }

    /**
     * Overi, zda je hodnota validni JSON.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function validateJson($value)
    {
        $valid = true;

        try {
            Json::decode($value);
        } catch (\Nette\Utils\JsonException $e) {
            $valid = false;
        }

        return $valid;
    }

    /**
     * Zvaliduje datovy typ hodnoty.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     */
    protected function validateType($key, $value, $type)
    {
        switch ($type) {
            case ExchangeMetadata::TYPE_INTEGER:
                $valid = Validators::isNumericInt($value);
            break;

            case ExchangeMetadata::TYPE_NUMBER:
                $valid = Validators::isNumeric($value);
            break;

            case ExchangeMetadata::TYPE_DATE_TIME:
                $valid = ($value instanceof \DateTime);
            break;

            default:
                $valid = Validators::is($value, $type);
        }


        if (!$valid) {
            $value = Dumper::toText($value);

            $this->addError('invalidObjectValueType', ['value' => $value, 'key' => $key, 'type' => $type]);
        }
    }

    /**
     * Zvaliduje ciselnikovou hodnotu.
     *
     * @param string $key
     * @param mixed $value
     * @param array $rule
     */
    protected function validateEnumValue($key, $value, array $rule)
    {
        if (isset($rule[ExchangeMetadata::RULE_ENUM])) {
            if (!in_array($value, $rule[ExchangeMetadata::RULE_ENUM], true)) {
                $this->addError('invalidObjectEnumValue', ['value' => $value, 'key' => $key, 'values' => implode(', ', $rule[ExchangeMetadata::RULE_ENUM])]);
            }
        }
    }

    /**
     * Zvaliduje zda hodnota lezi mezi extremy.
     *
     * @param string $key
     * @param mixed $value
     * @param array $rule
     */
    protected function validateExtremeValue($key, $value, array $rule)
    {
        if (isset($rule[ExchangeMetadata::RULE_MINIMUM])) {
            if ($value < $rule[ExchangeMetadata::RULE_MINIMUM]) {
                $this->addError('invalidObjectMinimalValue', ['value' => $value, 'key' => $key, 'extreme' => $rule[ExchangeMetadata::RULE_MINIMUM]]);
            }
        }

        if (isset($rule[ExchangeMetadata::RULE_MAXIMUM])) {
            if ($value > $rule[ExchangeMetadata::RULE_MAXIMUM]) {
                $this->addError('invalidObjectMaximalValue', ['value' => $value, 'key' => $key, 'extreme' => $rule[ExchangeMetadata::RULE_MAXIMUM]]);
            }
        }
    }

    /**
     * @param string $message
     * @param array $arguments
     */
    protected function addError($message, array $arguments = [])
    {
        ImportLogger::addError($this->object, $message, $arguments);
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
        return IValidator::TYPE_QUALITY;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return IValidator::TYPE_QUALITY;
    }
}
