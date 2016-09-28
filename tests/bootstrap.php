<?php declare(strict_types = 1);

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

	$configurator->addConfig(__DIR__ . '/../config/config.neon');
	$configurator->addConfig(__DIR__ . '/../config/config.local.neon');
	$configurator->addConfig(__DIR__ . '/tests.neon');
});

define('WWW_DIR', __DIR__ . '/../www');
