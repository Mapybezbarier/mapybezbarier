<?php

namespace MP\Mapper;

use Dibi\Connection;
use MP\Util\Lang\Lang;
use MP\Util\Transaction\DibiTransaction;
use Nette\DI\Container;
use Nette\Utils\Strings;

/**
 * Tovarna pro databazovy mapper.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class DatabaseMapperFactory
{
    /** @var Context */
    protected $context;

    /** @var Connection */
    protected $connection;

    /** @var DibiTransaction */
    protected $transaction;

    /** @var Lang */
    protected $lang;

    /** @var Container */
    protected $container;

    /** @var IMapper[] */
    protected $mappers = [];

    /**
     * @param Context $context
     * @param Connection $connection
     * @param DibiTransaction $transaction
     * @param Lang $lang
     * @param Container $container
     */
    public function __construct(Context $context, Connection $connection, DibiTransaction $transaction, Lang $lang, Container $container)
    {
        $this->context = $context;
        $this->connection = $connection;
        $this->transaction = $transaction;
        $this->lang = $lang;
        $this->container = $container;
    }

    /**
     * Vytvori instanci databazoveho mapperu dle nazvu manazera a databazove tabulky.
     *
     * @param string $managerName
     * @param string $tableName
     *
     * @return IMapper
     */
    public function create($managerName, $tableName)
    {
        if (!isset($this->mappers[$tableName])) {
            $mapperServiceName = $this->generateMapperServiceName($managerName);

            if ($this->container->hasService($mapperServiceName)) {
                $mapper = $this->container->getService($mapperServiceName);
            } else {
                $mapper = $this->createDefaultInstance($this->context, $this->connection, $this->transaction, $this->lang);
            }

            $mapper->setTable($tableName);

            $this->mappers[$tableName] = $mapper;
        }

        return $this->mappers[$tableName];
    }

    /**
     * Vytrvori vychozi implementaci databazoveho mapperu.
     *
     * @param Context $context
     * @param Connection $connection
     * @param DibiTransaction $transaction
     * @param Lang $lang
     *
     * @return DatabaseMapper
     */
    protected function createDefaultInstance(Context $context, Connection $connection, DibiTransaction $transaction, Lang $lang)
    {
        $instance = new DatabaseMapper($context, $connection, $transaction, $lang);

        return $instance;
    }

    /**
     * Vytvori nazev databazoveho mapperu dle nazvu manazera a konvence pojmenovani.
     *
     * @param string $managerName
     *
     * @return string
     */
    protected function generateMapperServiceName($managerName)
    {
        $services = $this->container->findByType($managerName);
        $serviceName = end($services);
        $serviceBaseName = Strings::substring($serviceName, 0, -Strings::length('Manager'));

        return Strings::firstLower($serviceBaseName) . "Mapper";
    }
}
