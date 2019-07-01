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

    public function autofill()
    {
        $objects = $this->objectService->getCamelCaseObjects([
            "certified", "accessibility_pram_id IS NULL OR accessibility_seniors_id IS NULL"
        ]);

        foreach ($objects as $object) {
            $object = $this->autofillAccessibility($object);

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
     *
     * @return array
     */
    public function autofillAccessibility(array $object)
    {
        // pro komunitni data nenastavuji
        if ($object['certified']) {
            $object = $this->autofillAccessibilityItem($object, 'accessibilityPram', ConsistencyValidatorPram::VALIDATOR_NAME);
            $object = $this->autofillAccessibilityItem($object, 'accessibilitySeniors', ConsistencyValidatorSeniors::VALIDATOR_NAME);
        }

        ImportLogger::reset();

        return $object;
    }

    /**
     * @param array $object
     * @return array
     */
    protected function autofillAccessibilityItem(array $object, string $accessibilityKey, string $validatorName): array
    {
        if (!isset($object[$accessibilityKey])) {
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
