<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

if (extension_loaded('xdebug')) {
	xdebug_disable();
	\Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

$configurator = new Nette\Configurator;
$configurator->setTempDirectory(__DIR__ . '/../temp');
$loader = $configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../app')
	->addDirectory(__DIR__ . '/../libs')
	->addDirectory(__DIR__ . '/../tests')
	->register();

define("WWW_DIR", __DIR__ . '/../www');

$configurator->addConfig(__DIR__ . '/../app/config/config.neon');
$configurator->addConfig(__DIR__ . '/../app/config/config.local.neon');
$container = $configurator->createContainer();

return $container;