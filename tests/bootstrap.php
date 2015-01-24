<?php

//TODO: test pro AOP, jestli jsou všechny \Model\ třídy zabezpečeny v aspektu...

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
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
$configurator->addParameters(['wwwDir' => __DIR__ . '/../www']); //because of %wwwDir% in config in CLI environment

$container = $configurator->createContainer();

return $container;