<?php

namespace MP\Component\Form;

use Nette\Application\UI\Form;
use Nette\ComponentModel\IContainer;
use Nette\Localization\ITranslator;

/**
 * Tovarna na formulare.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class FormFactory
{
    /** @var ITranslator */
    protected $translator;

    /**
     * FormFactory constructor.
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param IContainer|null $parent
     * @param string|null $name
     *
     * @return Form
     */
    public function create(IContainer $parent = null, $name = null)
    {
        $form = new Form($parent, $name);
        $form->setTranslator($this->translator);

        // metodu getToken je nutne volat pred inicializaci Nette session, ktera se inicializuje lazy (smart)
        // muze totiz dojit k tomu, ze volani addProtection probiha az ve chvili, kdy jiz byl zaslan nejaky vystup pri
        // vykresleni sablony, formulare se totiz typicky vytvareni lazy a zde je jiz na nastartovani session pozde
        $form->addProtection($this->translator->translate('messages.form.error.csrf'))->getToken();

        $this->prepareRenderer($form);

        return $form;
    }

    /**
     * @param Form $form
     */
    protected function prepareRenderer(Form $form)
    {
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = "div class='form_inner'";
        $renderer->wrappers['pair']['container'] = "div class='form_pair'";
        $renderer->wrappers['label']['container'] = "div class='form_label'";
        $renderer->wrappers['control']['container'] = "div class='form_item'";
    }
}
