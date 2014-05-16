<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">
<style>
	body { color: #333; background: white; width: 500px; margin: 100px auto }
	h1 { font: bold 47px/1.5 sans-serif; margin: .6em 0 }
	p { font: 21px/1.5 Georgia,serif; margin: 1.5em 0 }
	small { font-size: 70%; color: gray }
</style>

<h1>Průvodce instalací</h1>
<p>
	Zdá se, že tato aplikace zatím není nainstalována. Pojďme to rychle napravit. Zabere to jen chvíli&hellip;
</p>

<!-- https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/DI/OrmExtension.php -->

<?php

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
	//file_put_contents('test.neon', \Nette\Neon\Neon::encode(['doctrine' => $vals]));
	dump(\Nette\Neon\Neon::encode(['doctrine' => $vals]));
}

exit;
