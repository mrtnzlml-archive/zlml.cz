<?php declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

class AuthenticatorTest extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;
	use \Testbench\TDoctrine;

	private $authenticator;

	public function __construct()
	{
		$this->authenticator = $this->getService(Model\Authenticator::class);
	}

	public function testWrongAuthenticate()
	{
		$auth = $this->authenticator;
		Assert::exception(function () use ($auth) {
			$auth->authenticate(['invalid_username', 'invalid_password']);
		}, Nette\Security\AuthenticationException::class, 'Uživatelské jméno není správné.');
	}

	public function testAuthenticate()
	{
		$em = $this->getEntityManager();
		$userEntity = new Entity\User;
		$userEntity->setUsername('martin');
		$userEntity->setPassword('password');
		$userEntity->setRole('what a bad bad model design');
		$em->persist($userEntity);
		$em->flush();

		$auth = $this->authenticator;
		Assert::exception(function () use ($auth) {
			$auth->authenticate(['martin', 'invalid_password']);
		}, Nette\Security\AuthenticationException::class, 'Zadané heslo není správné.');
	}

	public function tearDown()
	{
		$em = $this->getEntityManager();
		/** @var Entity\User $user */
		foreach ($em->getRepository(Entity\User::class)->findBy([
			'username' => 'martin',
		]) as $user) {
			$em->remove($user);
		}
		$em->flush();
	}

}

(new AuthenticatorTest())->run();
