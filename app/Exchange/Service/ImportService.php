<?php

namespace MP\Exchange\Service;

use MP\Exchange\Parser\IParser;
use MP\Exchange\Parser\ParserFactory;
use MP\Exchange\Validator\ValidatorsFactory;
use MP\Module\Web\Service\ObjectService;
use MP\Service\GeocodingService;
use MP\Util\Transaction\DibiTransaction;
use Tracy\Debugger;

/**
 * Sluzba pro import mapovych objektu z podporovanych formatu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ImportService
{
    /** @var ParserFactory */
    protected $parserFactory;

    /** @var ValidatorsFactory */
    protected $validatorsFactory;

    /** @var ObjectService */
    protected $objectService;

    /** @var DibiTransaction */
    protected $transaction;

    /** @var RuianFinder */
    protected $ruianFinder;

    /** @var ValuesNormalizer */
    protected $valuesNormalizer;

    /** @var GeocodingService */
    protected $geocodingService;

    /**
     * @param ParserFactory $parserFactory
     * @param ValidatorsFactory $validatorsFactory
     * @param ObjectService $objectService
     * @param RuianFinder $ruianFinder
     * @param DibiTransaction $transaction
     * @param ValuesNormalizer $valuesNormalizer
     * @param GeocodingService $geocodingService
     */
    public function __construct(
        ParserFactory $parserFactory,
        ValidatorsFactory $validatorsFactory,
        ObjectService $objectService,
        RuianFinder $ruianFinder,
        DibiTransaction $transaction,
        ValuesNormalizer $valuesNormalizer,
        GeocodingService $geocodingService
    )
    {
        $this->parserFactory = $parserFactory;
        $this->validatorsFactory = $validatorsFactory;
        $this->objectService = $objectService;
        $this->ruianFinder = $ruianFinder;
        $this->transaction = $transaction;
        $this->valuesNormalizer = $valuesNormalizer;
        $this->geocodingService = $geocodingService;
    }

    /**
     * @param mixed $data
     * @param array $source
     * @param array $license
     * @param bool $certified
     * @param int $userId
     * @param bool $manual
     *
     * @return array
     */
    public function import($data, $source, $license, $certified, $userId, $manual = false)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '2048M');

        $objects = [];

        $parser = $this->parserFactory->create($source);

        try {
            $objects = $parser->parse($data);
        } catch (\MP\Exchange\Exception\ParseException $e) {
            ImportLogger::addError([], $e->getMessage());
        }

        if ($objects) {
            $this->prepareObjects($objects, $source, $license, $certified, $userId, $parser);
            $this->validateObjects($objects, $source);

            if (!ImportLogger::hasErrors()) {
                if (IParser::TYPE_INTERNAL === $parser->getType()) {
                    $this->internalImport($objects, $manual);
                } else if (IParser::TYPE_EXTERNAL === $parser->getType()) {
                    $this->externalImport($objects, $source, $manual);
                } else {
                    throw new \Nette\InvalidStateException("Unsupported parser type '{$parser->getType()}'");
                }

                // pri manualnim importu rovnou spustim dohledani GPS souradnic
                if ($manual) {
                    $this->geocodingService->processQueue(true);
                }
            }
        }

        return $objects;
    }

    /**
     * Zpracovani importu objektu z interniho zdroje.
     *
     * Pri vkladani zaznamu dochazi na parovani zaznamu. V pripade, ze zaznam v databazi jiz existuje a v importu se
     * nachazi zaznam s novejsim datem mapovani, je do databaze vlozena nova verze zaznamu a stara je archivovana.
     *
     * @param array $objects
     * @param bool $priority
     */
    protected function internalImport(array $objects, $priority)
    {
        $this->transaction->begin();

        $i = 0;

        foreach ($objects as $object) {
            $current = $this->objectService->getCurrentObject($object);

            if (null === $current || $current['modified_date'] < $object['modifiedDate']) {
                if (null !== $current) {
                    ImportLogger::addNotice($object, 'currentObject');
                }

                $i++;
                $this->objectService->saveObject($object, $priority, $current);
            }
        }

        ImportLogger::setCount($i);

        $this->transaction->commit();
    }

    /**
     * Zpracovani importu objektu z externiho zdroje.
     *
     * Pri vkladani zaznamu nedochazi k parovani a naslednemu verzovani zaznamu. Exsitujici zaznamy jsou smazany a
     * nahrazeny novymi.
     *
     * @param array $objects
     * @param array $source
     * @param bool $priority
     */
    protected function externalImport(array $objects, $source, $priority)
    {
        $this->transaction->begin();

        $this->objectService->removeObjectsBySource($source);

        foreach ($objects as $object) {
            $this->objectService->saveObject($object, $priority);
        }

        ImportLogger::setCount(count($objects));

        $this->transaction->commit();
    }

    /**
     * Pripravi objekt pro import.
     *
     * @param array $objects
     * @param array $source
     * @param array $license
     * @param bool $certified
     * @param int $userId
     * @param IParser $parser
     */
    protected function prepareObjects(array &$objects, $source, $license, $certified, $userId, $parser)
    {
        foreach ($objects as &$object) {
            $this->valuesNormalizer->normalize($object);

            $object['sourceId'] = $source['id'];
            $object['certified'] = $certified;

            if (
                IParser::TYPE_INTERNAL === $parser->getType()
                || (empty($object['latitude']) && empty($object['longitude']))
            ) {
                $object['ruianAddress'] = $this->ruianFinder->find($object);
            }

            $object['userId'] = $userId;
            $object['license'] = $license['title'];

            // nektere sloupce ze schematu nejsou urceny pro import
            unset($object['photoUrl']);
            unset($object['objectId']);
            unset($object['parentObjectId']);
        }
    }

    /**
     * Provede validaci objektu.
     *
     * @param array $objects
     * @param array $source
     */
    protected function validateObjects(array $objects, $source)
    {
        $validators = $this->validatorsFactory->create($source);

        foreach ($objects as $object) {
            foreach ($validators as $validator) {
                try {
                    $validator->validate($object);
                } catch (\MP\Exchange\Exception\ValidationException $e) {
                    Debugger::log($e, Debugger::EXCEPTION);

                    ImportLogger::addError($object, $e->getMessage());
                }

                if (ImportLogger::hasErrors()) {
                    break;
                }
            }
        }
    }
}
