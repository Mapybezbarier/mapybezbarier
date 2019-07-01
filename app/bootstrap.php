<?php

use Dibi\Bridges\Tracy\Panel;
use MP\Util\Tracy\Logger;
use Tracy\Debugger;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->addParameters([
    'storageDir' => STORAGE_DIR,
    'backupDir' => BACKUP_DIR,
    'databaseIp' => DATABASE_IP,
]);

$configurator->setDebugMode(file_exists(APP_DIR . '/config/config.local.neon'));
$configurator->enableDebugger(TRACY_DIR, BUGREPORT_MAIL);

$configurator->setTempDirectory(TEMP_DIR);

$configurator->createRobotLoader()
    ->addDirectory(APP_DIR)
    ->register();

Debugger::setLogger(new Logger(Debugger::$logDirectory, Debugger::$email, Debugger::getBlueScreen()));

$configurator->addConfig(APP_DIR . '/config/config.neon');

if (file_exists(APP_DIR . '/config/config.local.neon')) {
    $configurator->addConfig(APP_DIR . '/config/config.local.neon');
}

if (file_exists(APP_DIR . '/config/config.test.neon')) {
    $configurator->addConfig(APP_DIR . '/config/config.test.neon');
}

if ($configurator->isDebugMode()) {
    if (file_exists(APP_DIR . '/config/config.debug.neon')) {
        $configurator->addConfig(APP_DIR . '/config/config.debug.neon');
    }

    if (class_exists(Panel::class)) {
        Panel::$maxLength = 10000;
    }
}

return $configurator->createContainer();
