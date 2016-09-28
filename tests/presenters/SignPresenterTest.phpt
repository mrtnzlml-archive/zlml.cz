<?php

namespace Test;

use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SignPresenterTest extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;
	use \Testbench\TPresenter;

	/** @var Nette\Security\User */
	private $user;

	public function setUp()
	{
		$this->user = $this->getService(Nette\Security\User::class);
	}

	public function testRenderIn()
	{
		$this->checkAction('Sign:in');
	}

	public function testRenderLoggedIn()
	{
		$this->logIn(1, 'admin');
		$this->checkRedirect('Sign:in', '/admin');
	}

	public function testRenderLogOut()
	{
		$this->logIn(1, 'admin');
		Tester\Assert::true($this->user->isLoggedIn());
		$this->checkForm('Admin:Admin:default', 'signOutForm', [], '/sign/in');
		Tester\Assert::false($this->user->isLoggedIn());
	}

	public function testSignInForm()
	{
		Tester\Assert::error(function () {
			$response = $this->checkForm('Sign:in', 'signInForm-signInForm', [
				'username' => 'Username',
				'password' => 'Password',
				'remember' => TRUE,
			]);
			Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		}, Tester\AssertException::class, 'Uživatelské jméno není správné.');
		Tester\Assert::false($this->user->isLoggedIn());

		Tester\Assert::error(function () {
			$response = $this->checkForm('Sign:in', 'signInForm-signInForm', [
				'username' => 'Username',
				'password' => 'Password',
				'remember' => FALSE,
			]);
			Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		}, Tester\AssertException::class, 'Uživatelské jméno není správné.');
		Tester\Assert::false($this->user->isLoggedIn());
	}

}

(new SignPresenterTest)->run();
