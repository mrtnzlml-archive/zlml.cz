<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new \Nette\Configurator;
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../libs')
	->register();

define('APP_DIR', __DIR__ . '/../app');
define('WWW_DIR', __DIR__ . '/../www');

$configurator->addConfig(__DIR__ . '/config/config.neon');
if (file_exists(__DIR__ . '/config/config.local.neon')) {
	$configurator->addConfig(__DIR__ . '/config/config.local.neon');
}

\AntispamControl::register();

$help = <<<HELP

Please configure connection to the database first! Use following options:

	php index.php -u <db-user> -n <db-name> [-p <db-pass>] [-d <db-driver=mysql|pgsql>]

HELP;

if (file_exists(__DIR__ . '/config/config.local.neon')) {
	$config = \Nette\Neon\Neon::decode(file_get_contents(__DIR__ . '/config/config.local.neon'));
}
if (isset($config) && is_array($config) && array_key_exists('doctrine', $config)) {
	return $configurator->createContainer();
} elseif (PHP_SAPI === 'cli') {
	$options = getopt('u:n:p:d:');
	if (!isset($options['u']) || !isset($options['n'])) {
		die($help);
	}
	require_once(__DIR__ . '/model/.install-cli.php');
	exit;
} else {
	require_once(__DIR__ . '/model/.install.php');
}
