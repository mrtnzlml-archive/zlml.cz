<?php

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';
$auth = $container->createInstance('\App\Authenticator');

Assert::exception(function () use ($auth) {
	$auth->authenticate(array('invalid_username', 'invalid_password'));
}, 'Nette\Security\AuthenticationException', 'Uživatelské jméno není správné.');

Assert::exception(function () use ($auth) {
	$auth->authenticate(array('martin', 'invalid_password'));
}, 'Nette\Security\AuthenticationException', 'Zadané heslo není správné.');