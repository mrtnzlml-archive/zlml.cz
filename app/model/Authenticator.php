<?php

namespace App;

use Kdyby;
use Nette\Security\Passwords;
use Nette;
use Nette\Utils\Strings;

/**
 * Class Authenticator
 * @package App
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator {

	private $users;

	/**
	 * @param Users $users
	 */
	public function __construct(Users $users) {
		$this->users = $users;
	}

	/**
	 * @param array $credentials
	 * @return Nette\Security\Identity|Nette\Security\IIdentity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;
		$password = $this->removeCapsLock($password);
		$user = $this->users->findOneBy(['username' => $username]);

		if (!$user) {
			throw new Nette\Security\AuthenticationException('Uživatelské jméno není správné.', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $user->password)) {
			throw new Nette\Security\AuthenticationException('Zadané heslo není správné.', self::INVALID_CREDENTIAL);
		} elseif (Passwords::needsRehash($user->password)) {
			$user->password = Passwords::hash($password);
			$this->users->save($user);
		} else {
			return new Nette\Security\Identity($user->id, $user->role);
		}
	}

	/**
	 * Fixes caps lock accidentally turned on.
	 * @param $password
	 * @return mixed
	 */
	private function removeCapsLock($password) {
		return $password === Strings::upper($password) ? Strings::lower($password) : $password;
	}

}
