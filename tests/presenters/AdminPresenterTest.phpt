<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class AdminPresenterTest extends Tester\TestCase {

	private $action;

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
	}

	public function setUp() {
		$this->tester->init('Admin');
		$this->tester->logIn();
	}

	public function testRenderDefault() {
		$this->action = 'default';
		$this->tester->testAction($this->action);
	}

	public function testRenderDefaultEdit() {
		$this->action = 'default';
		$this->tester->testAction($this->action, 'GET', [1]);
	}

	public function testRenderPictures() {
		$this->action = 'pictures';
		$this->tester->testAction($this->action);
	}

	public function testRenderPrehled() {
		$this->action = 'prehled';
		$this->tester->testAction($this->action);
	}

	public function testRenderTags() {
		$this->action = 'tags';
		$this->tester->testAction($this->action);
	}

	public function tearDown() {
		$this->tester->logOut();
		$response = $this->tester->test($this->action);
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

}

$test = new AdminPresenterTest($container);
$test->run();