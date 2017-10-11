<?php

namespace MP\Module\Api\Presenters;

use MP\Exchange\Response\ObjectsResponse;
use MP\Exchange\Service\ExportService;
use MP\Manager\ExchangeSourceManager;
use MP\Module\Api\Service\ObjectRestrictorBuilder;
use MP\Module\Web\Service\ObjectService;

/**
 * Pristup ke zdrojum objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectPresenter extends ApiPresenter
{
    /** @var ExchangeSourceManager @inject */
    public $sourceManager;

    /** @var ObjectService @inject */
    public $objectService;

    /** @var ExportService @inject */
    public $exportService;

    /** @var ObjectRestrictorBuilder @inject */
    public $restrictorBuilder;

    /**
     * Metoda API /objects
     *
     * @param string $format
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function actionObjects($format)
    {
        $source = $this->sourceManager->findOneBy([["[format] = %s", $format]]);

        if ($source) {
            ini_set('memory_limit', '2048M');
            $restrictor = $this->restrictorBuilder->getRestrictor();

            $objects = $this->objectService->getObjects($restrictor);

            $payload = $this->exportService->export($objects, $source);

            $response = new ObjectsResponse($source, $payload);

            $this->sendResponse($response);
        } else {
            throw new \MP\Module\Api\Exception\ApiException("Unsupported format '{$format}'.");
        }
    }
}
