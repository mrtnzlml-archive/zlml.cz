<!DOCTYPE html>
<meta charset="utf-8">
<meta name="robots" content="noindex">
<title>New installation</title>
<link rel="stylesheet" type="text/css" href="css/install.css">

<h1>New installation</h1>
<p>
	Looks like you are just about to start with this application so let's set it up&hellip;
</p>

<?php
$security = <<<NEON
#
# SECURITY WARNING: it is CRITICAL that this file & directory are NOT accessible directly via a web browser!
#
# If you don't protect this directory from direct web access, anybody will be able to see your passwords.
# http://nette.org/security-warning
#\n\n
NEON;

$drivers = [];
if (extension_loaded('pdo_mysql')) {
	$drivers['pdo_mysql'] = 'MySQL';
}
if (extension_loaded('pdo_pgsql')) {
	$drivers['pdo_pgsql'] = 'PostgreSQL';
}
if (empty($drivers)) {
	echo "
	<div class='alert alert-danger'>
		Looks like you haven't installed any of supported database drivers. Supported database drivers are:
		<ul>
			<li>pdo_mysql</li>
			<li>pdo_pgsql</li>
		</ul>
	</div>";
	exit();
}

$form = new Nette\Forms\Form;
$form->addSelect('driver', NULL, $drivers);
$form->addText('host')->setRequired()->setValue('127.0.0.1');
$form->addText('port')->setDefaultValue('3306'); //pgsql: 5432
$form->addText('dbname')->setRequired();
$form->addText('user')->setRequired();
$form->addPassword('pass');

$form->addText('username')->setRequired();
$form->addPassword('password')->setRequired();

$form->addSubmit('create', 'Install');

$form->render('begin');
$form->render('errors');

echo $form['driver']->control->setClass('form-control');
echo $form['host']->control->setClass('form-control')->setPlaceholder('Server address');
echo $form['port']->control->setClass('form-control')->setPlaceholder('Database port');
echo $form['dbname']->control->setClass('form-control')->setPlaceholder('Database name');
echo $form['user']->control->setClass('form-control')->setPlaceholder('Database user');
echo $form['pass']->control->setClass('form-control')->setPlaceholder('Database password');

echo $form['username']->control->setClass('form-control')->setPlaceholder('Admin username (admin)');
echo $form['password']->control->setClass('form-control')->setPlaceholder('Admin password');

echo $form['create']->control->setClass('btn btn-large btn-primary btn-block');
$form->render('end');

if ($form->isSubmitted() && $form->isValid()) {
	$vals = $form->getValues();

	try {
		file_put_contents(__DIR__ . '/../config/config.local.neon', $security . \Nette\Neon\Neon::encode(['doctrine' => [
				'host' => $vals->host,
				'port' => $vals->port ? (int)$vals->port : 80,
				'user' => $vals->user,
				'password' => $vals->pass,
				'dbname' => $vals->dbname,
				'driver' => $vals->driver,
			]]));
		ob_start();
		$config = new \Nette\Configurator();
		$container = $config->setTempDirectory(__DIR__ . '/../../temp')
			->addParameters([
				'appDir' => __DIR__ . '/../../app',
			])->addConfig(__DIR__ . '/../config/config.neon')
			->addConfig(__DIR__ . '/../config/config.local.neon')
			->createContainer();

		/** @var \Kdyby\Doctrine\EntityManager $em */
		$em = $container->getByType('\Kdyby\Doctrine\EntityManager');

		/** @var \Kdyby\Doctrine\Connection $conn */
		$conn = $container->getByType('\Kdyby\Doctrine\Connection');

		$schemaTool = new Doctrine\ORM\Tools\SchemaTool($em);
		$schemaTool->updateSchema($em->getMetadataFactory()->getAllMetadata());

		$admin = new \Entity\User;
		$admin->username = $vals->username;
		$admin->password = \Nette\Security\Passwords::hash($vals->password);
		$admin->role = "admin";
		$em->persist($admin);

		$demo = new \Entity\User;
		$demo->username = "demo";
		$demo->password = \Nette\Security\Passwords::hash("demo");
		$demo->role = "demo";
		$em->persist($demo);

		if ($vals->driver == 'pdo_mysql') {
			$settingSql = file_get_contents(__DIR__ . '/../../sql/settings-mysql.sql');
			/** @var PDOStatement $setting */
			$setting = $conn->prepare($settingSql);
			$setting->execute();

			//Doctrine fulltext workaround:
			$workaroundSql = file_get_contents(__DIR__ . '/../../sql/fulltext-workaround-mysql.sql');
			/** @var PDOStatement $workaround */
			$workaround = $conn->prepare($workaroundSql);
			$workaround->execute();
		} else {
			$settingSql = file_get_contents(__DIR__ . '/../../sql/settings-pgsql.sql');
			/** @var PDOStatement $setting */
			$setting = $conn->prepare($settingSql);
			$setting->execute();
		}

		$post = new \Entity\Post;
		$title = 'Welcome to your new blog!';
		$post->title = $title;
		$post->slug = Nette\Utils\Strings::webalize($title);
		$post->body = 'The installation was successful. Yaay! (-:';
		$post->date = new \DateTime;
		$post->publish_date = new \DateTime;
		$em->persist($post);

		$em->flush();

		echo "<div class='alert alert-success'><strong>OK</strong> The installation was successful, please press F5 and reload this page again.</div>";
	} catch (\Exception $exc) {
		file_put_contents(__DIR__ . '/../config/config.local.neon', $security);
		echo "<div class='alert alert-danger'><strong>ERROR (#" . $exc->getCode() . "):</strong> " . $exc->getMessage() . "</div>";
	}
}

exit();
