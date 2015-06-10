<?php

namespace Test;

use Model;
use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class AdminPresenterTest extends \CustomTestCase
{

	/** @var Model\Users */
	private $users;
	/** @var Model\Posts */
	private $articles;

	private $action;

	public function setUp()
	{
		$this->openPresenter('Admin:Admin:');
		$this->users = $this->getService('\Model\Users');
		$this->articles = $this->getService('\Model\Posts');
		$this->logIn(1, 'admin');
	}

	public function testRenderDefault()
	{
		$this->checkAction($this->action = 'default');
	}

	public function testRenderDefaultEdit()
	{
		$article = $this->users->findOneBy([]);
		$this->checkAction($this->action = 'default', 'GET', [$article->getId()]);
	}

	public function testRenderPictures()
	{
		$this->checkAction($this->action = 'pictures');
	}

	public function testRenderPrehled()
	{
		$this->checkAction($this->action = 'prehled');
	}

	public function testRenderTags()
	{
		$this->checkAction($this->action = 'tags');
	}

	public function testRenderUsers()
	{
		$this->checkAction($this->action = 'users');
	}

	public function testRenderUserEdit()
	{
		$user = $this->users->findOneBy([]);
		$this->checkAction($this->action = 'userEdit', 'GET', [$user->getId()]);
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
		$this->logOut();
		$this->checkRedirect($this->action);
	}

}

(new AdminPresenterTest)->run();
