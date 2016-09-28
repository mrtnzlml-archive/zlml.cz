<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Testbench\Bootstrap::setup(__DIR__ . '/_temp', function (\Nette\Configurator $configurator) {
	$configurator->addParameters([
		'appDir' => __DIR__ . '/../app',
		'wwwDir' => __DIR__ . '/../www',
	]);

	$configurator->addConfig(__DIR__ . '/../app/config/config.neon');
	$configurator->addConfig(__DIR__ . '/../app/config/config.local.neon');
	$configurator->addConfig(__DIR__ . '/tests.neon');
});

$loader = new \Nette\Loaders\RobotLoader();
$loader->setCacheStorage(new \Nette\Caching\Storages\MemoryStorage());
$loader->addDirectory(__DIR__ . '/../app');
$loader->addDirectory(__DIR__ . '/../src');
$loader->addDirectory(__DIR__ . '/../tests');
$loader->register();

define("WWW_DIR", __DIR__ . '/../www');
