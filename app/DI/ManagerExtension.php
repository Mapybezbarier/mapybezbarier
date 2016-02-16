<?php

namespace MP\DI;

use MP\Manager\IManager;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;

/**
 * Extenze pro konfiguraci manazeru.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ManagerExtension extends CompilerExtension
{
    /** Tag znacici ze se jedna o manazera. Hodnota tagu je databazova tabulka, kterou manazer spravuje. */
    const TAG = 'mp.manager';

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        /** @var $serviceDefinition ServiceDefinition */
        foreach ($builder->getDefinitions() as $name => $serviceDefinition) {
            $class = $serviceDefinition->getClass();

            if (null !== $class && is_subclass_of($class, IManager::class)) {
                $this->setupTable($serviceDefinition);
            }
        }
    }

    /**
     * Na zaklade tagu manazera mu nastavit spravovanou tabulku.
     *
     * @param ServiceDefinition $serviceDefinition
     */
    private function setupTable(ServiceDefinition $serviceDefinition)
    {
        $tableName = $serviceDefinition->getTag(self::TAG);

        if ($tableName) {
            $serviceDefinition->addSetup('setTable', [$tableName]);
            $serviceDefinition->addSetup('init');
        } else {
            throw new \Nette\InvalidStateException("Manager '{$serviceDefinition->getClass()}' has no tag " . self::TAG . ".");
        }
    }
}
