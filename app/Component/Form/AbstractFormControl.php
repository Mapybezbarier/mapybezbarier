<?php

namespace MP\Component\Form;

use MP\Component\AbstractControl;

/**
 * Abstraktni predek komponent s formularem.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
abstract class AbstractFormControl extends AbstractControl
{
    /** @var FormFactory */
    protected $factory;

    /**
     * @param FormFactory $factory
     */
    public function __construct(FormFactory $factory)
    {
        $this->factory = $factory;
    }
}
