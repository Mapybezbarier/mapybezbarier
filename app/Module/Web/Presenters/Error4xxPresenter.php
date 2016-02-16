<?php

namespace MP\Module\Web\Presenters;

use Nette;
use Nette\Http\IResponse;

/**
 * Error presenter pro 4xx chyby.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Error4xxPresenter extends Nette\Application\UI\Presenter
{
    /** @const HTTP schema, pod kterym bezi WP aplikace */
    const SCHEME = 'http';
    /** @const Nazev subdomeny, kde bezi WP aplikace */
    const SUBDOMAIN = 'web.mapybezbarier.cz';
    /** @const Cislo portu, kde bezi WP aplikace */
    const PORT = 80;

    /**
     * @param \Exception $exception
     */
    public function renderDefault(\Exception $exception)
    {
        if (IResponse::S404_NOT_FOUND == $exception->getCode()) {
            $url = $this->getHttpRequest()->getUrl();

            $url->setScheme(self::SCHEME);
            $url->setHost(self::SUBDOMAIN);
            $url->setPort(self::PORT);

            $this->redirectUrl($url->getAbsoluteUrl(), IResponse::S301_MOVED_PERMANENTLY);
        } else {
            // load template 403.latte or 404.latte or ... 4xx.latte
            $file = __DIR__ . "/../templates/Error/{$exception->getCode()}.latte";
            $this->template->setFile(is_file($file) ? $file : __DIR__ . '/../templates/Error/4xx.latte');
        }
    }
}
