<?php

$container = require __DIR__ . '/../bootstrap.php';
$container->createInstance('\Model\Authenticator');

$model = new \Model\Authenticator();

$hash = '$2a$07$qy8p5nd1fyne2da1m7vgpe5SnizvXsZm3OxOp5M9fJ8Lsd1Ckcnp2';
\Tester\Assert::same($hash, $model->calculateHash('password', $hash));
\Tester\Assert::same($hash, $model->calculateHash('PASSWORD', $hash));

//FIXME: no idea how to get model classes...
//\Tester\Assert::exception($model->authenticate(array('user', 'pass')), 'AuthenticationException', 'The username is incorrect.');