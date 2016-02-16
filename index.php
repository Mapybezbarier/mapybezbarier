<?php

require __DIR__ . '/app/constant.php';
require __DIR__ . '/app/Util/shortcuts.php';

$container = require __DIR__ . '/app/bootstrap.php';
$container->getByType('Nette\Application\Application')->run();
