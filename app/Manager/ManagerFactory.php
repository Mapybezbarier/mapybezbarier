<?php

namespace MP\Manager;

use MP\DI\ManagerExtension;
use MP\Mapper\DatabaseMapperFactory;
use MP\Util\Lang\Lang;
use Nette\DI\Container;

/**
 * Tovarna na manazery.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class ManagerFactory
{
    /** @var DatabaseMapperFactory */
    protected $mapperFactory;

    /** @var Lang */
    protected $lang;

    /** @var Container */
    protected $context;

    /** @var array */
    protected $managers;

    /**
     * @param DatabaseMapperFactory $mapperFactory
     * @param Lang $lang
     * @param Container $context
     */
    public function __construct(DatabaseMapperFactory $mapperFactory, Lang $lang, Container $context)
    {
        $this->mapperFactory = $mapperFactory;
        $this->lang = $lang;
        $this->context = $context;
    }

    /**
     * @param string $tableName
     * @return IManager
     */
    public function create($tableName)
    {
        if (!isset($this->managers[$tableName])) {
            $managerServiceName = $this->findManagerServiceName($tableName);

            if ($managerServiceName) {
                $manager = $this->context->getService($managerServiceName);
            } else {
                $manager = $this->createDefaultInstance($this->mapperFactory, $this->lang, $tableName);
            }

            $this->managers[$tableName] = $manager;
        }

        $this->managers[$tableName]->setTable($tableName);

        return $this->managers[$tableName];
    }

    /**
     * Vytrvori vychozi implementaci databazoveho mapperu.
     *
     * @param DatabaseMapperFactory $mapperFactory
     * @param Lang $lang
     * @param string $table
     * @return DefaultManager
     */
    protected function createDefaultInstance(DatabaseMapperFactory $mapperFactory, Lang $lang, $table)
    {
        $instance = new DefaultManager($mapperFactory, $lang);
        $instance->setTable($table);
        $instance->init();

        return $instance;
    }

    /**
     * Dohleda nazev sluzby manazera, ktery spravuje danou databazovou tabulku.
     *
     * @param string $tableName
     * @return string|null
     */
    protected function findManagerServiceName($tableName)
    {
        $managerServiceNames = [];

        foreach ($this->context->findByTag(ManagerExtension::TAG) as $serviceName => $table) {
            if ($table === $tableName) {
                $managerServiceNames[] = $serviceName;
            }
        }

        if (1 < count($managerServiceNames)) {
            throw new \Nette\InvalidStateException("Multiple manager services registered for table '{$tableName}'. There must be only one.");
        }

        $managerServiceName = ($managerServiceNames ? reset($managerServiceNames) : null);

        return $managerServiceName;
    }
}
