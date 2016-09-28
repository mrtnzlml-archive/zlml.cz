<?php declare(strict_types=1);

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

// Let bootstrap create Dependency Injection container.
$container = require __DIR__ . '/../app/bootstrap.php';

try {
	// Run application.
	$container->getService('application')->run();
} catch (\Doctrine\DBAL\Exception\ConnectionException $exc) {
	require_once(__DIR__ . '/.maintenance.php');
}
