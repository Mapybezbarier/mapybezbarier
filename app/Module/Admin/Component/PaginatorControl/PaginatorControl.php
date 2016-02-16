<?php

namespace MP\Module\Admin\Component\PaginatorControl;

use MP\Component\AbstractControl;
use Nette\Utils\Paginator;

/**
 * Komponenta pro vykresleni strankovadla.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class PaginatorControl extends AbstractControl
{
    /**
     * @persistent
     * @var int
     */
    public $page;

    /** @var callable[] */
    public $onPageChange = [];

    /** @var Paginator */
    protected $paginator;

    /**
     * @param Paginator $paginator
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->paginator = $this->paginator;
        $template->steps = $this->prepareSteps();
        $template->render();
    }

    /**
     * @return array
     */
    protected function prepareSteps()
    {
        $page = $this->paginator->page;

        if (2 > $this->paginator->pageCount) {
            $steps = [$page];
        } else {
            $count = 4;

            $quotient = ($this->paginator->pageCount - 1) / $count;

            $range = range(max($this->paginator->firstPage, $page - 3), min($this->paginator->lastPage, $page + 3));

            for ($i = 0; $i <= $count; $i++) {
                $range[] = round($quotient * $i) + $this->paginator->firstPage;
            }

            sort($range);

            $steps = array_values(array_unique($range));
        }

        return $steps;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @param  array
     *
     * @return void
     */
    public function loadState(array $params)
    {
        parent::loadState($params);

        $this->paginator->page = $this->page;
    }

    /**
     * @param int $page
     */
    public function handleSetPage($page)
    {
        $this->onPageChange($this, $page);
    }

    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}
