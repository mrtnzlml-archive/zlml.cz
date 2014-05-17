<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">
<style>
	body { color: #333; background: white; width: 500px; margin: 100px auto }
	h1 { font: bold 47px/1.5 sans-serif; margin: .6em 0 }
	p { font: 21px/1.5 Georgia, serif; margin: 1.5em 0 }
	small { font-size: 70%; color: gray }
</style>

<h1>Průvodce instalací</h1>
<p>
	Zdá se, že tato aplikace zatím není nainstalována. Pojďme to rychle napravit. Zabere to jen chvíli&hellip;
</p>

<!-- https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/DI/OrmExtension.php -->

<?php

$security = <<<NEON
#
# SECURITY WARNING: it is CRITICAL that this file & directory are NOT accessible directly via a web browser!
#
# If you don't protect this directory from direct web access, anybody will be able to see your passwords.
# http://nette.org/security-warning
#\n\n
NEON;
$form = new Nette\Forms\Form;
$form->addSelect('database', 'Typ databáze', array(
	'pdo_mysql' => 'MySQL',
));
$form->addText('dbname', 'Název databáze')->setRequired();
$form->addText('host', 'Hostname')->setValue('127.0.0.1')->setRequired();
$form->addText('user', 'Uživatel databáze')->setRequired();
$form->addPassword('pass', 'Heslo k databázi');
$form->addSubmit('create', 'Nainstaloval projekt');
echo $form;

if ($form->isSubmitted() && $form->isValid()) {
	$vals = $form->getValues();
	try {
		//$pdo = new PDO("mysql:host=$vals->host;dbname=$vals->dbname", $vals->user, $vals->pass);
		//$sql = file_get_contents(__DIR__ . '/../zeminem.sql');
		//$stmt = $pdo->prepare($sql);
		//$stmt->execute();
		//TODO: import triggerů (?)
	} catch (PDOException $exc) {
		echo $exc->getMessage();
	}
	file_put_contents(__DIR__ . '/config/config.local.neon', $security . \Nette\Neon\Neon::encode(['doctrine' => [
			'user' => $vals->user,
			'password' => $vals->pass,
			'dbname' => $vals->dbname,
		]]));
	//header("Location:");
}

exit;
