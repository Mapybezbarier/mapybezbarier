<?php

namespace MP\Util;

use Nette\Reflection\AnnotationsParser;
use Nette\Reflection\ClassType;
use Nette\Utils\Strings;

/**
 * Helpery pro praci s tridami.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Classes
{
    /**
     * Vraci slozku, ve ktere je umistena trida
     *
     * @param string $class
     *
     * @return null|string null v pripade, ze trida nebyla nalezena
     */
    public static function getClassDir($class)
    {
        $dir = null;

        if (class_exists($class)) {
            $reflection = new ClassType($class);
            $dir = dirname($reflection->getFileName());
        }

        return $dir;
    }

    /**
     * Normalizuje nazev tridy - tj. z namespacu osekne uvodni \, pro jistotu otrimuje od white-spacu
     *
     * @param string $className
     *
     * @return string normalizovany nazev tridy
     */
    public static function fixClassName($className)
    {
        return trim(ltrim($className, "\\"));
    }

    /**
     * Ziskani nazvu tridy z FQN nazvu tridy (jako Reflection::getShortName(), ale nepouziva se reflexe (v pripade, ze
     * je vstupem string)ale pouze se jednotlive casti rozdeli pres \ a vrati se posledni cast.
     *
     * @param string|\ReflectionClass $fullClassName
     * @return string
     * @throws \Nette\InvalidArgumentException
     */
    public static function getShortName($fullClassName)
    {
        if ($fullClassName instanceof \ReflectionClass) {
            return $fullClassName->getShortName();
        } elseif (false === is_string($fullClassName)) {
            throw new \Nette\InvalidArgumentException("Expected string or instance of \\ReflectionClass, got " . self::getType($fullClassName));
        }

        $parts = explode('\\', $fullClassName);

        return end($parts);
    }

    /**
     * Rozsireni PHPckovskeho gettype. Kdyz je dana promenna typu object nebo resource, pokusi se vypsat dodatecne info
     * o tride objektu nebo typu resource, popr. kousek poslaneho stringu
     *
     * @param mixed $var
     *
     * @return string
     */
    public static function getType(&$var)
    {
        $type = gettype($var);

        if ('object' === $type) {
            $class = get_class($var);
            $type .= " of $class";

            if (method_exists($var, '__toString')) {
                try {
                    $type .= ' [' . Strings::truncate((string)$var, self::$maxLen) . ']';
                } catch (\Exception $e) {
                }
            }
        } elseif ('resource' === $type) {
            $resourceType = get_resource_type($var);

            if ($type !== 'Unknown') {
                $type .= " [$resourceType]";
            }
        } elseif ('string' === $type) {
            $type .= ' (' . Strings::length($var) . ') [' . Strings::truncate($var, self::$maxLen) . ']';
        }

        return $type;
    }

    /**
     * Expanduje nazev tridy (muze byt relativni) na absolutni (vzhledem k danemu reflektoru).
     * Na expanzi vyuziva Nettacky @\Nette\Reflection\AnnotationsParser().
     *
     * Pokud se nepodari expandovat dany nazev tridy, vraci vstupni argument - className
     *
     * @param string $className
     * @param \ReflectionClass $reflector
     *
     * @return string
     */
    public static function expandClassName($className, \ReflectionClass $reflector)
    {
        $class = $className;

        $fixClassName = self::fixClassName($className);

        if (false === class_exists($className)) {
            $className = AnnotationsParser::expandClassName($fixClassName, $reflector);
        }

        if (false === class_exists($className)) {
            $className = $class;
        }

        return self::fixClassName($className);
    }
} 
