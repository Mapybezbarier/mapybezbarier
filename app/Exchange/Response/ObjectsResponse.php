<?php

namespace MP\Exchange\Response;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\SmartObject;

/**
 * Aplikacni odpoved pro objekty. Vyuzivano pri exportu a v ramci API.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ObjectsResponse implements \Nette\Application\IResponse
{
    use SmartObject;

    /** @const Zname typy odpovedi. Slouzi pro nastaveni mime-type odpovedi. */
    const XML = 'xml',
        JSON = 'json',
        CSV = 'csv';

    /** @const Kodovani odpovedi. */
    const CHARSET = 'UTF-8';

    /** @var array */
    protected $source;

    /** @var string */
    protected $payload;

    /**
     * @param array $source
     * @param string $payload
     */
    public function __construct(array $source, $payload)
    {
        $this->source = $source;
        $this->payload = $payload;
    }

    /**
     * Sends response to output.
     *
     * @param IRequest $httpRequest
     * @param IResponse $httpResponse
     */
    public function send(IRequest $httpRequest, IResponse $httpResponse)
    {
        switch ($this->source['format']) {
            case self::XML:
                $httpResponse->setContentType("application/xml", self::CHARSET);
            break;

            case self::JSON:
                $httpResponse->setContentType("application/json", self::CHARSET);
            break;

            case self::CSV:
                $httpResponse->setContentType("text/csv", self::CHARSET);
            break;

            default:
                throw new \Nette\InvalidArgumentException("Unsupported response format '{$this->source['format']}'.");
        }

        echo $this->payload;
    }
}
