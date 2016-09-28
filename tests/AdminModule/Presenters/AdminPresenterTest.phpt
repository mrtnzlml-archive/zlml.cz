<?php declare(strict_types = 1);

namespace App\Tests\AdminModule\Presenters;

use App\Users\Users;
use Tester;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class AdminPresenterTest extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;
	use \Testbench\TPresenter;

	/** @var Users */
	private $users;

	private $action = NULL;

	public function setUp()
	{
		$this->users = $this->getService(Users::class);
		$this->logIn(1, 'admin');
	}

	public function testRenderDefault()
	{
		$this->checkAction($this->action = 'Admin:Admin:default');
	}

	public function testRenderDefaultEdit()
	{
		$article = $this->users->findOneBy([]);
		$this->checkAction($this->action = 'Admin:Admin:default', [$article->getId()]);
	}

	public function testRenderPictures()
	{
		$this->checkAction($this->action = 'Admin:Admin:pictures');
	}

	public function testRenderPrehled()
	{
		$this->checkAction($this->action = 'Admin:Admin:prehled');
	}

	public function testRenderTags()
	{
		$this->checkAction($this->action = 'Admin:Admin:tags');
	}

	public function testRenderUsers()
	{
		$this->checkAction($this->action = 'Admin:Admin:users');
	}

	public function testRenderUserEdit()
	{
		$user = $this->users->findOneBy([]);
		$this->checkAction($this->action = 'Admin:Admin:userEdit', [$user->getId()]);
	}

	public function testRenderUserEditForm()
	{
		Tester\Assert::error(function () {
			$this->checkForm('Admin:Admin:userEdit', 'userEditForm-form', [
				'username' => 'Username',
				'password' => 'Password',
				'role' => 'admin',
			]);
		}, \Tester\AssertException::class, "field 'passwordVerify' returned this error(s):\n  - Zadejte prosím heslo ještě jednou pro kontrolu.");

		Tester\Assert::error(function () {
			$this->checkForm('Admin:Admin:userEdit', 'userEditForm-form', [
				'username' => 'Username',
				'password' => 'Password',
				'passwordVerify' => 'Password2',
				'role' => 'admin',
			]);
		}, \Tester\AssertException::class, "field 'passwordVerify' returned this error(s):\n  - Hesla se neshodují.");

		$this->checkForm('Admin:Admin:userEdit', 'userEditForm-form', [
			'username' => 'Username',
			'password' => 'Password',
			'passwordVerify' => 'Password',
			'role' => 'admin',
		], '/admin/users');

		//FIXME: Invalid security token for signal 'deleteUser'
//		$user = $this->users->findOneBy(['username' => 'Username']);
//		$response = $this->checkSignal('Admin:Admin:users', 'deleteUser', [
//			'user_id' => $user->getId(),
//		]);
//		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

	public function tearDown()
	{
		if ($this->action === NULL) {
			return;
		}

		$this->logOut();
		$this->checkRedirect($this->action, '/auth/sign/in');
	}

}

(new AdminPresenterTest)->run();
