<?php

namespace MP\Util;

use Nette\Reflection\ClassType;
use ReflectionClass;

trait TReflection
{
    /**
     * Nahrada odebrane funkce z \Nette\Object::getReflection()
     *
     * @return ClassType|ReflectionClass
     */
    public static function getReflection()
    {
        $class = class_exists(ClassType::class) ? ClassType::class : ReflectionClass::class;

        return new $class(get_called_class());
    }
}
