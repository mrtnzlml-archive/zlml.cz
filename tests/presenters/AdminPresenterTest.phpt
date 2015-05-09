<?php

namespace Test;

use Model;
use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class AdminPresenterTest extends Tester\TestCase
{

	/** @var Model\Users */
	private $users;
	/** @var Model\Posts */
	private $articles;

	private $action;

	public function __construct(Nette\DI\Container $container)
	{
		$this->tester = new PresenterTester($container);
		$this->users = $container->getByType('\Model\Users');
		$this->articles = $container->getByType('\Model\Posts');
	}

	public function setUp()
	{
		$this->tester->init('Admin:Admin');
		$this->tester->logIn(1, 'admin');
	}

	public function testRenderDefault()
	{
		$this->action = 'default';
		$this->tester->testAction($this->action);
	}

	public function testRenderDefaultEdit()
	{
		$this->action = 'default';
		$article = $this->users->findOneBy([]);
		$this->tester->testAction($this->action, 'GET', [$article->getId()]);
	}

	public function testRenderPictures()
	{
		$this->action = 'pictures';
		$this->tester->testAction($this->action);
	}

	public function testRenderPrehled()
	{
		$this->action = 'prehled';
		$this->tester->testAction($this->action);
	}

	public function testRenderTags()
	{
		$this->action = 'tags';
		$this->tester->testAction($this->action);
	}

	public function testRenderUsers()
	{
		$this->action = 'users';
		$this->tester->testAction($this->action);
	}

	public function testRenderUserEdit()
	{
		$this->action = 'userEdit';
		$user = $this->users->findOneBy([]);
		$this->tester->testAction($this->action, 'GET', [$user->getId()]);
	}

	/**
	 * @skip
	 */
	public function testRenderUserEditForm()
	{
		/*$response = $this->tester->test('userEdit', 'POST', array(
			'do' => 'userEditForm-form-submit',
		), array(
			'username' => 'Username',
			'password' => 'Password',
			'role' => 'demo',
			'token' => 'token'
		));
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);*/
		//FIXME: CSRF
		/*$user = $this->users->findOneBy(['username' => 'Username']);
		$response = $this->tester->test('users', 'GET', array(
			'do' => 'deleteUser',
		), array(
			'user_id' => $user->getId(),
		));
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);*/
	}

	public function tearDown()
	{
		$this->tester->logOut();
		$response = $this->tester->test($this->action);
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

}

$test = new AdminPresenterTest($container);
$test->run();
