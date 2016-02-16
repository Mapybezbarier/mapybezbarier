<?php

namespace MP\Module\Web\Component;

use MP\Component\AbstractControl;
use MP\Util\RuntimeMode;
use MP\Util\Strings;

/**
 * Komponenta pro GA
 */
class GAControl extends AbstractControl
{
    /** @var array */
    protected $settings;

    /** @var RuntimeMode */
    public $runtimeMode;

    /**
     * @param array $settings
     * @param RuntimeMode $runtimeMode
     */
    public function __construct(array $settings, RuntimeMode $runtimeMode)
    {
        $this->runtimeMode = $runtimeMode;
        $this->settings = $settings;
    }

    public function render()
    {
        if (!$this->runtimeMode->isDebugMode()) {
            $template = $this->getTemplate();
            $template->settings = $this->generateGASettings();
            $template->render();
        }
    }

    /**
     * Pripravi data pro zdrojovy kod GA
     * @return array[
     *          'puvodni_kod'
     *          'otrimovany_kod_bez_pomlcek'
     *         ]
     */
    private function generateGASettings()
    {
        if (!empty($this->settings['code'])) {
            return [
                $this->settings['code'],
                Strings::replace($this->settings['code'], '/-/', ''),
            ];
        }
    }
}
