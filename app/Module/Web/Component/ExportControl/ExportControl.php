<?php

namespace MP\Module\Web\Component;

use MP\Component\AbstractControl;
use MP\Exchange\Response\ObjectsResponse;
use MP\Module\Web\Service\ObjectRestrictorBuilder;
use MP\Util\Lang\Lang;
use Nette\Application\UI\ITemplate;
use Nette\Http\Url;

/**
 * Komponenta pro vypis informaci o moznostech exportu s odkazy na export aktualni mnoziny objektu dle filtru
 */
class ExportControl extends AbstractControl
{
    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /** @var Lang */
    protected $lang;

    /**
     * @param ObjectRestrictorBuilder $restrictorBuilder
     * @param Lang $lang
     */
    public function __construct(ObjectRestrictorBuilder $restrictorBuilder, Lang $lang)
    {
        $this->restrictorBuilder = $restrictorBuilder;
        $this->lang = $lang;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->render();
    }

    /**
     * @return string
     */
    public function toString()
    {
        $template = $this->getTemplate();

        return (string) $template;
    }

    /**
     * @override Priprava odkazu na API
     *
     * @param string|null $file
     *
     * @return ITemplate
     */
    public function getTemplate($file = null)
    {
        $template = parent::getTemplate($file);

        $query = $this->restrictorBuilder->getActiveQueryData(true);

        foreach ([ObjectsResponse::XML, ObjectsResponse::JSON, ObjectsResponse::CSV] as $format) {
            $link = $this->getPresenter()->link('//:Api:Object:objects', ['locale' => $this->lang->getLocale(), 'format' => $format]);
            $link = new Url($link);
            $link->setQuery($query);

            $template->add("{$format}Link", $link->getAbsoluteUrl());
        }

        return $template;
    }
}
