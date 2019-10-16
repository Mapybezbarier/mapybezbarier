<?php

namespace MP\Module\Admin\Service;

use MP\Exchange\Service\ImportLogger;
use MP\Exchange\Validator\ConsistencyValidatorPram;
use MP\Exchange\Validator\ConsistencyValidatorSeniors;
use MP\Exchange\Validator\ValidatorsFactory;
use MP\Manager\ObjectManager;
use MP\Object\ObjectMetadata;



/**
 * Sluzba pro doplneni pristupnosti pro rodice s detmi a seniory.
 */
class AutofillAccessibilityService
{
    /** @var ObjectManager */
    protected $objectManager;
    /**
     * @var ObjectService
     */
    protected $objectService;
    /**
     * @var ValidatorsFactory
     */
    protected $validatorsFactory;

    /**
     * @param ObjectService $objectService
     * @param ObjectManager $objectManager
     * @param ValidatorsFactory $validatorsFactory
     */
    public function __construct(
        ObjectService $objectService,
        ObjectManager $objectManager,
        ValidatorsFactory $validatorsFactory
    ) {
        $this->objectManager = $objectManager;
        $this->objectService = $objectService;
        $this->validatorsFactory = $validatorsFactory;
    }

    public function autofill(bool $force) : int
    {
        if ($force) {
            $restrictor = ["certified"];
        } else {
            $restrictor = [
                "certified", "accessibility_pram_id IS NULL OR accessibility_seniors_id IS NULL"
            ];
        }

        $objects = $this->objectService->getCamelCaseObjects($restrictor);

        foreach ($objects as $object) {
            $object = $this->autofillAccessibility($object, $force);

            $saveItem = [
                'accessibility_pram' => $object['accessibilityPram'],
                'accessibility_seniors' => $object['accessibilitySeniors'],
                'id' => $object['id'],
            ];
            $this->objectManager->persist($saveItem);
        }

        return count($objects);
    }

    /**
     * Automaticke doplneni pristupnosti pro rodice s detmi a seniory, pokud neni explicitne zadano
     * @param array $object
     * @param bool $force
     *
     * @return array
     */
    public function autofillAccessibility(array $object, bool $force = false) : array
    {
        // pro komunitni data nenastavuji
        if ($object['certified']) {
            $object = $this->autofillAccessibilityItem(
                $object, 'accessibilityPram', ConsistencyValidatorPram::VALIDATOR_NAME, $force
            );
            $object = $this->autofillAccessibilityItem(
                $object, 'accessibilitySeniors', ConsistencyValidatorSeniors::VALIDATOR_NAME, $force
            );
        }

        ImportLogger::reset();

        return $object;
    }

    /**
     * @param array $object
     * @return array
     */
    protected function autofillAccessibilityItem(array $object, string $accessibilityKey, string $validatorName, bool $force): array
    {
        if ($force || !isset($object[$accessibilityKey])) {
            $validator = $this->validatorsFactory->getByName($validatorName);

            ImportLogger::reset();

            if ($validator) {
                $object[$accessibilityKey] = ObjectMetadata::ACCESSIBILITY_OK;
                $validator->validate($object);

                if (ImportLogger::hasErrors() || ImportLogger::hasNotices()) {
                    $object[$accessibilityKey] = ObjectMetadata::ACCESSIBILITY_PARTLY;
                    ImportLogger::reset();
                    $validator->validate($object);

                    if (ImportLogger::hasErrors() || ImportLogger::hasNotices()) {
                        $object[$accessibilityKey] = ObjectMetadata::ACCESSIBILITY_NO;
                    }
                }
            }
        }

        return $object;
    }
}
