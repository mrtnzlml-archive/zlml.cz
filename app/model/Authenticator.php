<?php

namespace Model;

use Nette\Security\Passwords;
use Nette;
use Nette\Utils\Strings;

/**
 * Class Authenticator
 * @package Model
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator {

	/** @var \Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $context) {
		$this->database = $context;
	}

	/**
	 * @param array $credentials
	 * @return Nette\Security\Identity|Nette\Security\IIdentity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;
		$row = $this->database->table('users')->where('username', $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if (!Passwords::verify($this->removeCapsLock($password), $row->password)) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} else {
			$row = Nette\ArrayHash::from($row);
			unset($row->password);
			return new Nette\Security\Identity($row->id, $row->role, $row);
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
