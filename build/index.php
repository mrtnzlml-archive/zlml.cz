    ____        _ __    __
   / __ )__  __(_) /___/ /__  _____
  / __  / / / / / / __  / _ \/ ___/
 / /_/ / /_/ / / / /_/ /  __/ /
/_____/\__,_/_/_/\__,_/\___/_/ 1.0.0

<?php

require 'tools/nette.phar';
use Nette\Utils\Finder;

$dir = "./blog";
if (strpos(dirname(__FILE__), '/', 0) !== false) {
	define('WINDOWS_SERVER', false);
} else {
	define('WINDOWS_SERVER', true);
}

echo "--- Setting up PHP..." . PHP_EOL;
set_time_limit(0);
date_default_timezone_set('Europe/Prague');

echo "--- Downloading project from git repository..." . PHP_EOL;
echo exec('git clone https://mrtnzlml@bitbucket.org/mrtnzlml/www.zeminem.cz.git blog') . PHP_EOL;

echo "--- Updating Composer..." . PHP_EOL;
echo exec("composer selfupdate");

echo "--- Installing dependencies... [can take a while]" . PHP_EOL;
$working_dir = realpath($dir);
//echo exec("composer update --working-dir $working_dir");

echo "--- Cleaning project..." . PHP_EOL;
foreach (Finder::findDirectories(".git")->from($working_dir)->childFirst() as $file) {
	delete($file);
}
foreach (Finder::findFiles(".git*")->from($working_dir) as $file) {
	delete($file);
}
delete($working_dir . DIRECTORY_SEPARATOR . '.travis.yml');
delete($working_dir . DIRECTORY_SEPARATOR . 'composer.json');
delete($working_dir . DIRECTORY_SEPARATOR . 'composer.lock');
delete($working_dir . DIRECTORY_SEPARATOR . '.git');
delete($working_dir . DIRECTORY_SEPARATOR . 'tests');
delete($working_dir . DIRECTORY_SEPARATOR . 'temp/cache');

echo "--- Creating ZIP archive..." . PHP_EOL;
$source = './blog/';
$destination = './blog.zip';
$zip = new ZipArchive();
if (!$zip->open($destination, ZIPARCHIVE::OVERWRITE)) {
	die("FATAL: Cannot create a ZIP archive!\n");
}
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
foreach ($files as $file) {
	$file = str_replace('\\', '/', $file);
	if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) { // Ignore "." and ".." folders
		continue;
	}
	echo "[Archive] $file\n";
	if (is_dir($file) === true) {
		$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	} elseif (is_file($file) === true) {
		$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
	}
}
$zip->close();

echo "----------------" . PHP_EOL;
echo "----- DONE -----" . PHP_EOL;
echo "----------------" . PHP_EOL;


function delete($fileName) {
	if (is_dir($fileName)) {
		echo " > Deleting directory $fileName" . PHP_EOL;
		foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fileName, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $item) {
			if (is_dir($item)) {
				rmdir($item);
			} else {
				unlink($item);
			}
		}
		rmdir($fileName);
	} elseif (is_file($fileName)) {
		echo " > Deleting file $fileName" . PHP_EOL;
		if (!WINDOWS_SERVER) {
			if (!unlink($fileName)) {
				echo " > ::ERR:: while deleting directory $fileName" . PHP_EOL;
			}
		} else {
			$lines = array();
			exec("DEL /F/Q \"$fileName\"", $lines, $deleteError);
		}
	}
}