<?php

namespace Test;

use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SignPresenterTest extends \CustomTestCase
{

	public function __construct()
	{
		$this->openPresenter('Sign:');
	}

	public function testRenderIn()
	{
		$this->checkAction('in');
	}

	public function testRenderLoggedIn()
	{
		$this->logIn(1, 'admin');
		$this->checkRedirect('in', '/admin');
	}

//	TODO: I have no idea how test it properly...
//	public function testRenderLogOut()
//	{
//		$presenter = $this->getPresenter();
//		$this->logIn(1, 'admin');
//		Tester\Assert::true($presenter->user->isLoggedIn());
//		$this->check('out');
//		Tester\Assert::false($presenter->user->isLoggedIn());
//	}

	public function testSignInForm()
	{
		$presenter = $this->getPresenter();
		$response = $this->check('in', [
			'do' => 'signInForm-signInForm-submit',
		], [
			'username' => 'Username',
			'password' => 'Password',
			'remember' => TRUE,
		]);
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::false($presenter->user->isLoggedIn());
		$response = $this->check('in', [
			'do' => 'signInForm-signInForm-submit',
		], [
			'username' => 'Username',
			'password' => 'Password',
			'remember' => FALSE,
		]);
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::false($presenter->user->isLoggedIn());
	}

}

(new SignPresenterTest)->run();
