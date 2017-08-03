<?php

namespace MP\Module\Web\Component;

use MP\Component\AbstractControl;
use MP\Module\Web\Service\ObjectRestrictorBuilder;
use Nette\Application\UI\ITemplate;

/**
 * Komponenta pro vypis odkaz pro vlozenou mapu
 */
class EmbeddedInfoControl extends AbstractControl
{
    /** @var ObjectRestrictorBuilder */
    protected $restrictorBuilder;

    /** @var array */
    protected $mapParams = [];

    /**
     * @param ObjectRestrictorBuilder $restrictorBuilder
     */
    public function __construct(ObjectRestrictorBuilder $restrictorBuilder)
    {
        $this->restrictorBuilder = $restrictorBuilder;
    }

    /**
     * @param array $mapParams
     */
    public function setMapParams($mapParams)
    {
        $this->mapParams = $mapParams;
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
     * @override Priprava odkazu vkladane mapy
     * @param string|null $file
     *
     * @return ITemplate
     */
    public function getTemplate($file = null)
    {
        $template = parent::getTemplate($file);

        $query = array_merge(
            $this->restrictorBuilder->getActiveQueryData(false),
            $this->mapParams
        );

        $template->link = $this->getPresenter()->link('//Embedded:default', $query);

        return $template;
    }
}
