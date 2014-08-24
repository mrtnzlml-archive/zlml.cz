<?php

namespace Test;

use Model;
use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * TODO: ověřit práva k akcím
 * @testCase
 */
class AdminPresenterTest extends Tester\TestCase {

	/** @var Model\Users */
	private $users;
	/** @var Model\Posts */
	private $articles;

	private $action;

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
		$this->users = $container->getByType('\Model\Users');
		$this->articles = $container->getByType('\Model\Posts');
	}

	public function setUp() {
		$this->tester->init('Admin');
		$this->tester->logIn(1, 'demo');
	}

	public function testRenderDefault() {
		$this->action = 'default';
		$this->tester->testAction($this->action);
	}

	public function testRenderDefaultEdit() {
		$this->action = 'default';
		$article = $this->users->findOneBy([]);
		$this->tester->testAction($this->action, 'GET', array($article->getId()));
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

	public function testRenderUsers() {
		$this->action = 'users';
		$this->tester->testAction($this->action);
	}

	public function testRenderUserEdit() {
		$this->action = 'userEdit';
		$user = $this->users->findOneBy([]);
		$this->tester->testAction($this->action, 'GET', array($user->getId()));
	}

	public function tearDown() {
		$this->tester->logOut();
		$response = $this->tester->test($this->action);
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

}

$test = new AdminPresenterTest($container);
$test->run();
