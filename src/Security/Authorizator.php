<?php declare(strict_types = 1);

namespace App\Security;

use Nette;
use Nette\Security\Permission;

class Authorizator implements Nette\Security\IAuthorizator
{

	private $acl;

	const CREATE = 'create';
	const READ = 'read';
	const UPDATE = 'update';
	const DELETE = 'delete';

	public function __construct()
	{
		$acl = new Nette\Security\Permission();
		// definice rolí
		$acl->addRole('guest');
		$acl->addRole('demo', 'guest'); // demo dědí od guest
		$acl->addRole('admin', 'demo'); // a od něj dědí admin
		// seznam zdrojů, ke kterým mohou uživatelé přistupovat
		$acl->addResource('Admin:Admin');
		$acl->addResource('Front');
		// pravidla, určující, kdo co může s čím dělat
		$acl->allow('guest', 'Front', self::READ);
		$acl->allow('demo', 'Admin:Admin', self::READ);
		$acl->allow('admin', Permission::ALL, Permission::ALL);
		// Nastaveno!
		$this->acl = $acl;
	}

	/**
	 * @return bool
	 */
	public function isAllowed($role, $resource, $privilege)
	{
		return $this->acl->isAllowed($role, $resource, $privilege);
	}

	/**
	 * @return bool
	 */
	public function isAtLeastInRole($role, Nette\Security\User $user)
	{
		$result = TRUE;
		foreach ($user->getRoles() as $userRole) {
			if ($userRole === $role) {
				return TRUE;
			}
			$result &= $this->acl->roleInheritsFrom($userRole, $role);
		}
		return (boolean)$result;
	}

}
