<?php

namespace MP\Module\Api\Presenters;

use DateInterval;
use MP\Manager\ApiQuotaManager;
use MP\Presenters\AbstractPresenter;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Http\Response;
use Nette\Utils\DateTime;
use Tracy\Debugger;

/**
 * Predek presenteru pro poskytovani zdroju API.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class ApiPresenter extends AbstractPresenter
{
    /** @const Kvota pristupu k API per IP adresa. */
    const RETRY_QUOTA = 10;

    /** @var ApiQuotaManager @inject */
    public $apiQuotaManager;

    /**
     * @override Overeni kvoty.
     * @throws \Nette\Application\ForbiddenRequestException
     *
     */
    protected function startup()
    {
        parent::startup();

        $this->checkQuota();
    }

    /**
     * Overi pristup k API z pohledu kvot.
     *
     * @throws \Nette\Application\ForbiddenRequestException
     */
    protected function checkQuota()
    {
        if (!$this->runtimeMode->isDebugMode()) {
            $ip = $this->getHttpRequest()->getRemoteAddress();

            if (null !== $ip) {
                $entry = $this->apiQuotaManager->findOneBy([["[ip] = %s", $ip]]);

                if ($entry) {
                    $now = DateTime::from(time());
                    $limit = DateInterval::createFromDateString(self::RETRY_QUOTA . ' seconds');
                    $margin = $entry['ping']->add($limit);

                    if ($now > $margin) {
                        $entry['ping'] = $now;

                        $this->apiQuotaManager->persist($entry);
                      } else {
                        /** @var DateInterval $retry */
                        $retry = $now->diff($margin);

                        $message = "API access quota exceeded. You can try again in {$retry->s} seconds.";

                        Debugger::log($message, Debugger::WARNING);

                        throw new \Nette\Application\ForbiddenRequestException($message);
                    }
                } else {
                    $this->apiQuotaManager->persist(['ip' => $ip]);
                }
            } else {
                $message = "Unresolved host";

                Debugger::log($message, Debugger::WARNING);

                throw new \Nette\Application\ForbiddenRequestException($message);
            }
        }
    }

    /**
     * @param Request $request
     *
     * @return IResponse
     * @throws \Nette\Application\BadRequestException
     */
    public function run(Request $request)
    {
        try {
            $reponse = parent::run($request);
        } catch (\MP\Module\Api\Exception\ApiException $e) {
            throw new \Nette\Application\BadRequestException($e->getMessage(), Response::S400_BAD_REQUEST);
        }

        return $reponse;
    }
}
