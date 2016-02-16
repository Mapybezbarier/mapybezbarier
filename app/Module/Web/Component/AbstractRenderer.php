<?php

namespace MP\Module\Web\Component;

use MP\Component\AbstractControl;
use Nette\Application\UI\ITemplate;

/**
 * Predek komponent pro vykresleni obsahu detailu mapoveho objektu.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractRenderer extends AbstractControl implements IRenderer
{
    /** @var array */
    protected $object;

    /**
     * @param array $object
     */
    public function __construct(array $object)
    {
        $this->object = $object;
    }

    /**
     * Vykresleni info boxu.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $this->prepareTemplateVars($template);

        $template->render();
    }

    /**
     * @param ITemplate $template
     */
    protected function prepareTemplateVars(ITemplate $template)
    {
        $template->object = $this->object;
    }
}
