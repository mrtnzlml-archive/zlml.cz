<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class Setting extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="string", name="`key`", unique=TRUE) */
	protected $key;

	/** @ORM\Column(type="string", name="`value`", nullable=TRUE) */
	protected $value;

}
