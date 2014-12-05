<?php

// Load Nette Framework or autoloader generated by Composer
require __DIR__ . '/../vendor/autoload.php';

$configurator = new \Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setDebugMode(FALSE);
$configurator->enableDebugger(__DIR__ . '/../log', 'mrtnzlml@gmail.com');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../libs')
	->register();

define("WWW_DIR", __DIR__ . '/../www');

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$help = <<<HELP

Please configure connection to the database first! Use following options:
	php index.php <db-user> <db-name> [<db-pass>]

HELP;

$config = \Nette\Neon\Neon::decode(file_get_contents(__DIR__ . '/config/config.local.neon'));
if (is_array($config) && array_key_exists('doctrine', $config)) {
	return $configurator->createContainer();
} elseif (PHP_SAPI === 'cli') {
	if (!isset($argv[2])) {
		die($help);
	}
	require_once(__DIR__ . '/model/.install-cli');
} else {
	require_once(__DIR__ . '/model/.install');
}
