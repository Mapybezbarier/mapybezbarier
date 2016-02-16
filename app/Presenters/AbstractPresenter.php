<?php

namespace MP\Presenters;

use CssMin;
use JShrink\Minifier;
use Kdyby\Autowired\AutowireComponentFactories;
use MP\Component\FlashMessageControl;
use MP\Component\IFlashMessageControlFactory;
use MP\Util\RuntimeMode;
use MP\Util\Strings;
use Nette\Application\UI\Presenter;
use WebLoader\Nette\CssLoader;
use WebLoader\Nette\JavaScriptLoader;
use WebLoader\Nette\LoaderFactory;

/**
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractPresenter extends Presenter
{
    use AutowireComponentFactories;

    /** @var RuntimeMode @inject */
    public $runtimeMode;

    /** @var LoaderFactory @inject */
    public $webLoader;

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->locale = $this->locale;
        $this->template->debugMode = $this->runtimeMode->isDebugMode();
        $this->template->stagingMode = $this->runtimeMode->isStagingMode();
    }

    /**
     * @return CssLoader
     */
    protected function createComponentCss()
    {
        $module = Strings::firstLower($this->getModule());

        $control = $this->webLoader->createCssLoader($module);

        if (!$this->runtimeMode->isDebugMode()) {
            $control->getCompiler()->addFilter(function ($code) {
                return CssMin::minify($code);
            });
        }

        return $control;
    }

    /**
     * @return JavaScriptLoader
     */
    protected function createComponentJs()
    {
        $module = Strings::firstLower($this->getModule());

        $control = $this->webLoader->createJavaScriptLoader($module);

        if (!$this->runtimeMode->isDebugMode()) {
            $control->getCompiler()->addFilter(function ($code) {
                return Minifier::minify($code);
            });
        }

        return $control;
    }

    /**
     * @param IFlashMessageControlFactory $factory
     *
     * @return FlashMessageControl
     */
    protected function createComponentFlashMessage(IFlashMessageControlFactory $factory)
    {
        $control = $factory->create();

        return $control;
    }

    /**
     * Vrati nazev modulu.
     *
     * @return string
     */
    protected function getModule()
    {
        $parts = explode(':', $this->getName());

        return reset($parts);
    }
}
