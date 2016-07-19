<?php

namespace MP\Module\Web\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

/**
 * Error presenter pro 4xx chyby.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Error4xxPresenter extends Presenter
{
    /** @const HTTP schema, pod kterym bezi WP aplikace */
    const SCHEME = 'http';
    /** @const Nazev subdomeny, kde bezi WP aplikace */
    const HOST = 'web.mapybezbarier.cz';
    /** @const Cislo portu, kde bezi WP aplikace */
    const PORT = 80;

    /** @var \Nette\Http\IRequest @inject */
    public $request;

    /**
     * @param \Exception $exception
     */
    public function actionDefault(\Exception $exception)
    {
        \Tracy\Debugger::log(var_export($this->request->getPost(), true), 'post');
        \Tracy\Debugger::log(var_export($this->request->getCookies(), true), 'cookies');

        if (IResponse::S404_NOT_FOUND == $exception->getCode()) {
            $url = $this->getHttpRequest()->getUrl();

            $url->setScheme(self::SCHEME);
            $url->setHost(self::HOST);
            $url->setPort(self::PORT);

            $this->redirectUrl($url->getAbsoluteUrl(), IResponse::S301_MOVED_PERMANENTLY);
        }
    }

    /**
     * @param \Exception $exception
     */
    public function renderDefault(\Exception $exception)
    {
        // load template 403.latte or 404.latte or ... 4xx.latte
        $file = __DIR__ . "/../templates/Error/{$exception->getCode()}.latte";
        $this->template->setFile(is_file($file) ? $file : __DIR__ . '/../templates/Error/4xx.latte');
    }
}
