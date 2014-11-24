<?php

namespace Model;

use Nette;
use Nette\Security\Permission;

/**
 * Class Authorizator
 * @package Model
 */
class Authorizator implements Nette\Security\IAuthorizator {

	private $acl;

	const CREATE = 'create';
	const READ = 'read';
	const UPDATE = 'update';
	const DELETE = 'delete';

	public function __construct() {
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

	function isAllowed($role, $resource, $privilege) {
		return $this->acl->isAllowed($role, $resource, $privilege);
	}

}
