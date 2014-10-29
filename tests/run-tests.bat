@ECHO OFF
SET BIN_TARGET=%~dp0/../vendor/nette/tester/Tester/tester.php
php "%BIN_TARGET%" -c php.ini --coverage-src ./../app/ --coverage ./coverage.html %* ./../tests
