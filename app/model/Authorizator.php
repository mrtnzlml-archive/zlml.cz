<?php

namespace App;

use Nette;
use Nette\Security\Permission;

class Authorizator implements Nette\Security\IAuthorizator {

	private $acl;

	const EDIT = 'edit';
	const VIEW = 'view';

	public function __construct() {
		$acl = new Nette\Security\Permission();
		// definice rolí
		$acl->addRole('guest');
		$acl->addRole('demo', 'guest'); // demo dědí od guest
		$acl->addRole('admin', 'demo'); // a od něj dědí admin
		// seznam zdrojů, ke kterým mohou uživatelé přistupovat
		$acl->addResource('Admin');
		// pravidla, určující, kdo co může s čím dělat
		$acl->allow('demo', 'Admin', self::VIEW);
		$acl->allow('admin', Permission::ALL, Permission::ALL);
		// Nastaveno!
		$this->acl = $acl;
	}

	function isAllowed($role, $resource, $privilege) {
		return $this->acl->isAllowed($role, $resource, $privilege);
	}

}