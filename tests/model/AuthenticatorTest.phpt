<?php

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class AuthenticatorTest extends \CustomTestCase
{

	//use \Test\CompiledContainer;

	private $authenticator;

	public function __construct()
	{
		$this->authenticator = $this->getService('Model\Authenticator');
	}

	public function testWrongAuthenticate()
	{
		$auth = $this->authenticator;
		Assert::exception(function () use ($auth) {
			$auth->authenticate(['invalid_username', 'invalid_password']);
		}, 'Nette\Security\AuthenticationException', 'Uživatelské jméno není správné.');
	}

	//TODO: nejdříve je potřeba připravit data pro testování
	//Assert::exception(function () use ($auth) {
	//	$auth->authenticate(array('martin', 'invalid_password'));
	//}, 'Nette\Security\AuthenticationException', 'Zadané heslo není správné.');

}

(new AuthenticatorTest())->run();
