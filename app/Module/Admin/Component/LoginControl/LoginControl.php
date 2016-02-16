<?php

namespace MP\Module\Admin\Component\LoginControl;

use MP\Component\Form\AbstractFormControl;
use MP\Component\Form\FormFactory;
use MP\Module\Admin\Presenters\AccessPresenter;
use MP\Module\Admin\Service\AccessService;
use MP\Util\RuntimeMode;
use Nette\Application\UI\Form;
use Nette\Http\Request;

/**
 * Komponenta pro prihlaseni uzivatele
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class LoginControl extends AbstractFormControl
{
    /** @const Nazvy komponent */
    const COMPONENT_LOGIN = 'login',
        COMPONENT_PASSWORD = 'password',
        COMPONENT_PERMANENT = 'permanent',
        COMPONENT_FORM = 'form';

    /** @var RuntimeMode */
    protected $runtimeMode;

    /** @var AccessService */
    protected $accessService;

    /** @var Request */
    protected $request;

    /**
     * @param FormFactory $factory
     * @param RuntimeMode $runtimeMode
     * @param AccessService $accessService
     * @param Request $request
     */
    public function __construct(
        FormFactory $factory,
        RuntimeMode $runtimeMode,
        AccessService $accessService,
        Request $request
    )
    {
        parent::__construct($factory);

        $this->runtimeMode = $runtimeMode;
        $this->accessService = $accessService;
        $this->request = $request;
    }

    public function render()
    {
        $template = $this->getTemplate();
        $template->debugMode = $this->runtimeMode->isDebugMode();
        $template->stagingMode = $this->runtimeMode->isStagingMode();
        $template->render();
    }

    /**
     * @param Form $form
     */
    public function loginUser(Form $form)
    {
        $values = $form->getValues(true);

        $error = $this->accessService->login(
            $values[self::COMPONENT_LOGIN], $values[self::COMPONENT_PASSWORD], (bool) $values[self::COMPONENT_PERMANENT]
        );

        if (!$error) {
            $this->getPresenter()->restoreRequest($values[AccessPresenter::PARAM_RESTORE]);
            $this->getPresenter()->redirect(':Admin:Dashboard:default');
        } else {
            $form->addError($error);
        }
    }

    /**
     * @param string $name
     *
     * @return Form
     */
    protected function createComponentForm($name)
    {
        $form = $this->factory->create($this, $name);
        $form->onSuccess[] = [$this, 'loginUser'];

        $form->addText(self::COMPONENT_LOGIN, 'backend.control.login.label.login')->setRequired(true)->setAttribute('autofocus');
        $form->addPassword(self::COMPONENT_PASSWORD, 'backend.control.login.label.password')->setRequired(true);
        $form->addCheckbox(self::COMPONENT_PERMANENT, 'backend.control.login.label.permanent');
        // nutne volat getControl(), aby se inicializovala session
        $form->addProtection('backend.control.login.error.protection')->getControl();
        $form->addHidden(AccessPresenter::PARAM_RESTORE, $this->request->getQuery(AccessPresenter::PARAM_RESTORE));
        $form->addSubmit('submit', 'backend.user.action.login');

        return $form;
    }
}
