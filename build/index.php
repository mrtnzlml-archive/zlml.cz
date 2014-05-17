____        _ __    __
/ __ )__  __(_) /___/ /__  _____
/ __  / / / / / / __  / _ \/ ___/
/ /_/ / /_/ / / / /_/ /  __/ /
/_____/\__,_/_/_/\__,_/\___/_/ 1.0.0

<?php

require 'tools/nette.phar';
use Nette\Utils\Finder;

$dir = "./blog";

echo "--- Setting up PHP..." . PHP_EOL;
set_time_limit(0);
date_default_timezone_set('Europe/Prague');

echo "--- Downloading project from git repository..." . PHP_EOL;
echo exec('git clone https://mrtnzlml@bitbucket.org/mrtnzlml/www.zeminem.cz.git blog') . PHP_EOL;

echo "--- Updating Composer..." . PHP_EOL;
echo exec("composer selfupdate");

echo "--- Installing dependencies..." . PHP_EOL;
$working_dir = realpath($dir);
echo exec("composer update --working-dir $working_dir");

echo "--- Cleaning project..." . PHP_EOL;
foreach (Finder::findDirectories(".git")->from($working_dir)->childFirst() as $file) {
	unlink($file); //http://stackoverflow.com/questions/12148229/how-to-get-permission-to-use-unlink
}
foreach (Finder::findFiles(".git*")->from($working_dir) as $file) {
	unlink($file);
}

//unlink travis atd...

echo "--- Creating ZIP archive..." . PHP_EOL;
$source = './blog/';
$destination = './blog.zip';
$zip = new ZipArchive();
if (!$zip->open($destination, ZIPARCHIVE::OVERWRITE)) {
	//TODO:die
}
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
foreach ($files as $file) {
	$file = str_replace('\\', '/', $file);
	//TODO: ignore file & folders (.git, tests)
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