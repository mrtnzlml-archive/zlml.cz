<?php declare(strict_types=1);

$position = $_SERVER['argc'] - 1;

if ($position === 0) {
	$_SERVER['argv'][++$position] = '.';
}
$_SERVER['argv'][++$position] = '-c';
$_SERVER['argv'][++$position] = 'php.ini';

// $_SERVER['argv'][++$position] = '--coverage';
// $_SERVER['argv'][++$position] = 'coverage.html';
// $_SERVER['argv'][++$position] = '--coverage-src';
// $_SERVER['argv'][++$position] = './../app';

$dir = __DIR__ . '/temp';

if (!is_dir($dir)) {
	mkdir($dir);
}

$rdi = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
$rii = new \RecursiveIteratorIterator($rdi, \RecursiveIteratorIterator::CHILD_FIRST);
foreach ($rii as $entry) {
	if ($entry->isDir()) {
		rmdir($entry);
	} else {
		unlink($entry);
	}
}

require __DIR__ . '/../vendor/bin/tester';
