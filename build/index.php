<?php

//require 'tools/Nette/nette.min.php';
//use Nette\Utils\Finder;

set_time_limit(0);
date_default_timezone_set('Europe/Prague');

echo exec('git clone https://mrtnzlml@bitbucket.org/mrtnzlml/www.zeminem.cz.git blog');
//echo exec('composer selfupdate');
//echo exec('composer update');

//foreach (Finder::findDirectories(".git")->from($dir)->childFirst() as $file) {
//	$project->delete($file);
//}

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
