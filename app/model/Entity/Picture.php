<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="pictures")
 */
class Picture extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="string", length=40) */
	public $uuid;

	/** @ORM\Column(type="string", length=255) */
	public $name;

	/** @ORM\Column(type="datetime") */
	public $created;

}
