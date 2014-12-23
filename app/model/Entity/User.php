<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(unique=true, type="string", length=100) */
	protected $username;

	/** @ORM\Column(type="string", length=100) */
	protected $password;

	/** @ORM\Column(type="string", length=20) */
	protected $role;

}
