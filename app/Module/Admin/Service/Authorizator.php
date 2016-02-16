<?php

namespace MP\Module\Admin\Service;

use Nette\Security\Permission;

/**
 * Sluzba pro autorizaci uzivatelu.
 *
 * Pokud nema role explicitne povolene vsechny akce (self::ALL), pak pokud neexistuje zdroj/alce je uzivateli pristup
 * odepren. V tomto pripade je potreba zdroj/akci pridat a v ramci role povolit.
 *
 * @author Martin Odstrcilik <martin.odstrcilik@gmail.com>
 */
class Authorizator extends Permission
{
    /** @const Uzivatelske role. */
    const ROLE_MASTER = 'master',
        ROLE_ADMIN = 'admin',
        ROLE_AGENCY = 'agency',
        ROLE_MAPPER = 'mapper',
        ROLE_GUEST = 'guest';

    /** @const Zdroje. */
    const RESOURCE_USER = 'User',
        RESOURCE_OBJECT = 'Object',
        RESOURCE_DRAFT = 'Draft',
        RESOURCE_COMPARE = 'Compare',
        RESOURCE_SYSTEM = 'System',
        RESOURCE_DASHBOARD = 'Dashboard',
        RESOURCE_IMPORT = 'Import';

    /** @const Seznam zdroju. */
    private static $RESOURCES = [
        self::RESOURCE_USER,
        self::RESOURCE_OBJECT,
        self::RESOURCE_DRAFT,
        self::RESOURCE_COMPARE,
        self::RESOURCE_SYSTEM,
        self::RESOURCE_DASHBOARD,
        self::RESOURCE_IMPORT,
    ];

    /** @const Akce. */
    const ACTION_VIEW = 'default',
        ACTION_CREATE = 'create',
        ACTION_EDIT = 'edit',
        ACTION_DELETE = 'delete',
        ACTION_JOIN = 'join',
        ACTION_SPLIT = 'split',
        ACTION_SELECT = 'select',
        ACTION_HISTORY = 'history',
        ACTION_BACKUP = 'backup',
        ACTION_MAPPING = 'mapping',
        ACTION_LOGS = 'logs';

    public function __construct()
    {
        $this->prepareRoles();
        $this->prepareResources();
        $this->preparePermissions();
    }

    /**
     * Pripravi ACL role.
     */
    private function prepareRoles()
    {
        $this->addRole(self::ROLE_MAPPER);
        $this->addRole(self::ROLE_AGENCY, self::ROLE_MAPPER);
        $this->addRole(self::ROLE_ADMIN, self::ROLE_AGENCY);
        $this->addRole(self::ROLE_MASTER, self::ROLE_ADMIN);
    }

    /**
     * Pripravi zdroje ACL.
     */
    private function prepareResources()
    {
        foreach (self::$RESOURCES as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * Pripravi prava.
     */
    private function preparePermissions()
    {
        // mapper
        $this->allow(self::ROLE_MAPPER, self::RESOURCE_OBJECT, [self::ACTION_VIEW, self::ACTION_CREATE, self::ACTION_EDIT, self::ACTION_SELECT, self::ACTION_MAPPING]);
        $this->allow(self::ROLE_MAPPER, self::RESOURCE_DRAFT, [self::ACTION_VIEW, self::ACTION_CREATE, self::ACTION_EDIT, self::ACTION_DELETE]);
        $this->allow(self::ROLE_MAPPER, self::RESOURCE_USER, [self::ACTION_VIEW, self::ACTION_EDIT]);
        $this->allow(self::ROLE_MAPPER, self::RESOURCE_SYSTEM, self::ACTION_VIEW);
        $this->allow(self::ROLE_MAPPER, self::RESOURCE_DASHBOARD, self::ALL);

        // agency
        $this->allow(self::ROLE_AGENCY, self::RESOURCE_USER, self::ACTION_CREATE);

        // admin
        $this->allow(self::ROLE_ADMIN, self::ALL, self::ALL);
    }

    /**
     * @override Kaskadovite dohledani.
     *
     * @param string $role
     *
     * @return array
     */
    public function getRoleParents($role)
    {
        $parents = [];

        foreach (parent::getRoleParents($role) as $role) {
            $parents = array_merge([$role], $this->getRoleParents($role));
        }

        return $parents;
    }
}
