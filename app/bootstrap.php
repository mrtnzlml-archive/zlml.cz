<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = new \Nette\Configurator;
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

define('APP_DIR', __DIR__ . '/../app');
define('WWW_DIR', __DIR__ . '/../www');

$configurator->addConfig(__DIR__ . '/../config/config.neon');
if (file_exists(__DIR__ . '/../config/config.local.neon')) {
	$configurator->addConfig(__DIR__ . '/../config/config.local.neon');
}

return $configurator->createContainer();
