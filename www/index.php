<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

// Let bootstrap create Dependency Injection container.
$container = require __DIR__ . '/../app/bootstrap.php';

define('APP_DIR', __DIR__ . '/../app');

// Try to run application else start new installation
try {
    $container->createContainer()->getService('application')->run();
} catch (Exception $exc) {
    include APP_DIR . "/model/.isInstallRequired";
    new isInstallRequired($exc);
}