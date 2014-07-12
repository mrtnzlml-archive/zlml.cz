<?php

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';
$xmlrpc = $container->createInstance('\App\Xmlrpc');

/**
 * @skip Call to undefined function xmlrpc_server_create (bacuse CLI?)
 */
Assert::exception(function () use ($xmlrpc) {
	$xmlrpc->pingback_ping('invalidSourceUrl', 'invalidTargetUrl');
}, 'TODO', 'TODO');
