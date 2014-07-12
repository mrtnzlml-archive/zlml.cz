<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SignPresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
	}

	public function setUp() {
		$this->tester->init('Sign');
	}

	public function testRenderIn() {
		$this->tester->testAction('in');
	}

	public function testRenderLoggedIn() {
		$this->tester->logIn(1, 'admin');
		$response = $this->tester->test('in');
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

	public function testRenderLogOut() {
		$presenter = $this->tester->getPresenter();
		$this->tester->logIn(1, 'admin');
		Tester\Assert::true($presenter->user->isLoggedIn());
		$this->tester->test('out');
		Tester\Assert::false($presenter->user->isLoggedIn());
	}

	public function testSignInForm() {
		$presenter = $this->tester->getPresenter();
		$response = $this->tester->test('in', 'POST', array(
			'do' => 'signInForm-submit',
		), array(
			'username' => 'Username',
			'password' => 'Password',
			'remember' => TRUE,
		));
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::false($presenter->user->isLoggedIn());
		$response = $this->tester->test('in', 'POST', array(
			'do' => 'signInForm-submit',
		), array(
			'username' => 'Username',
			'password' => 'Password',
			'remember' => FALSE,
		));
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::false($presenter->user->isLoggedIn());
	}

}

$test = new SignPresenterTest($container);
$test->run();
