<?php

//TODO: test pro AOP, jestli jsou všechny \Model\ třídy zabezpečeny v aspektu...

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

$loader = new \Nette\Loaders\RobotLoader();
$loader->setCacheStorage(new \Nette\Caching\Storages\MemoryStorage());
$loader->addDirectory(__DIR__ . '/../app');
$loader->addDirectory(__DIR__ . '/../libs');
$loader->addDirectory(__DIR__ . '/../tests');
$loader->register();

define("WWW_DIR", __DIR__ . '/../www');

Test\Bootstrap::setup(__DIR__);
