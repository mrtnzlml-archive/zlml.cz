<?php

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';
$auth = $container->createInstance('\Model\Authenticator');

Assert::exception(function () use ($auth) {
	$auth->authenticate(['invalid_username', 'invalid_password']);
}, 'Nette\Security\AuthenticationException', 'Uživatelské jméno není správné.');

//TODO: nejdříve je potřeba připravit data pro testování
//Assert::exception(function () use ($auth) {
//	$auth->authenticate(array('martin', 'invalid_password'));
//}, 'Nette\Security\AuthenticationException', 'Zadané heslo není správné.');