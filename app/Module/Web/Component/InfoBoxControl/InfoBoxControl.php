<?php

namespace MP\Module\Web\Component\InfoBoxControl;

use MP\Component\AbstractControl;
use MP\Module\SourceDetail\DetailService;
use MP\Module\Web\Component\InfoBoxControl\Renderer\IRenderer;
use MP\Module\Web\Component\IRendererFactory;
use MP\Module\Web\Component\RendererFactory;
use MP\Util\Paginator\PaginatorFactory;
use Nette\Application\UI\ITemplate;
use Nette\Utils\Paginator;

/**
 * Komponenta pro vykresleni info boxu markeru Google mapy.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class InfoBoxControl extends AbstractControl
{
    /**
     * @persistent
     * @var int[]
     */
    public $ids = [];

    /**
     * @persistent
     * @var int
     */
    public $page = 0;

    /** @var DetailService */
    protected $detailService;

    /** @var RendererFactory */
    protected $rendererFactory;

    /** @var Paginator */
    protected $paginator;

    /** @var  array */
    protected $object;

    /** @var bool vykreslovano v embedded mape? */
    protected $embedded = false;

    /**
     * @param DetailService $detailService
     * @param PaginatorFactory $paginatorFactory
     * @param IRendererFactory $rendererFactory
     */
    public function __construct(DetailService $detailService, PaginatorFactory $paginatorFactory, IRendererFactory $rendererFactory)
    {
        $this->detailService = $detailService;
        $this->paginator = $paginatorFactory->create(1, $this->page);
        $this->rendererFactory = $rendererFactory;
    }

    public function render()
    {
        $this->beforeRender();

        $template = $this->getTemplate();
        $template->render();
    }

    public function renderDetail()
    {
        $this->beforeRender();

        $template = $this->getTemplate('.detail');
        $template->render();
    }

    /**
     * @return string
     */
    public function toString()
    {
        $this->beforeRender();

        $template = $this->getTemplate();

        return (string) $template;
    }

    /**
     * @param string|null $file
     *
     * @return ITemplate
     */
    public function getTemplate($file = null)
    {
        if (null === $file) {
            if (1 < count($this->ids)) {
                $template = parent::getTemplate('.group');
                $template->paginator = $this->paginator;
            } else {
                $template = parent::getTemplate('.object');
            }
        } else {
            $template = parent::getTemplate($file);
        }

        $template->object = $this->object;
        $template->embedded = $this->embedded;

        return $template;
    }

    /**
     * @param array $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @param int[] $ids
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @param bool $embedded
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = $embedded;
    }

    /**
     * @param int $page
     *
     * @throws \Nette\Application\BadRequestException
     */
    public function handleSetPage($page)
    {
        if ($this->getPresenter()->isAjax()) {
            $this->setPage($page);
            $this->redrawControl('object');
        } else {
            throw new \Nette\Application\BadRequestException;
        }
    }

    protected function beforeRender()
    {
        $this->paginator->setItemCount(count($this->ids));
        $this->paginator->setPage($this->page);

        if (null === $this->object) {
            $this->object = $this->detailService->getDetailById($this->ids[$this->paginator->getOffset()]);
        }
    }

    /**
     * @return IRenderer
     */
    protected function createComponentRenderer()
    {
        /** @var IRenderer $renderer */
        $renderer = $this->rendererFactory->create($this->object);
        $renderer->setEmbedded($this->embedded);

        return $renderer;
    }
}
