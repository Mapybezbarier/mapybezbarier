<?php

namespace MP\Module\Web\Presenters;

use Nette;
use Nette\Application\Request;
use Nette\Application\Responses\CallbackResponse;
use Nette\Application\Responses\ForwardResponse;
use Nette\SmartObject;
use Tracy\ILogger;

/**
 * Vychozi error presenter
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ErrorPresenter implements Nette\Application\IPresenter
{
    use SmartObject;

    /** @var ILogger */
    private $logger;

    /**
     * @param ILogger $logger
     */
    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return CallbackResponse|ForwardResponse
     */
    public function run(Request $request)
    {
        $exception = $request->getParameter('exception');

        if ($exception instanceof Nette\Application\BadRequestException) {
            return new ForwardResponse($request->setPresenterName('Web:Error4xx'));
        }

        $this->logger->log($exception, ILogger::EXCEPTION);

        return new CallbackResponse(function () {
            require __DIR__ . '/../templates/Error/500.phtml';
        });
    }

}
