<?php

namespace MP\Util\Latte\Factory;

use MP\Util\Latte\Filter\FilterSet;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory as NTemplateFactory;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class TemplateFactory extends NTemplateFactory
{
    /** @var FilterSet @inject */
    public $filterSet;

    /**
     * @param Control|null $control
     *
     * @return Template
     */
    public function createTemplate(Control $control = null)
    {
        $template = parent::createTemplate($control);
        
        $this->registerFilters($template);
        
        return $template;
    }

    /**
     * @param Template $template
     */
    private function registerFilters(Template $template)
    {
        foreach (FilterSet::FILTERS as $name => $filter) {
            $template->addFilter(is_numeric($name) ? $filter : $name, [$this->filterSet, $filter]);
        }
    }
}
