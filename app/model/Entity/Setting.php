<?php declare(strict_types = 1);

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="settings")
 */
class Setting extends Doctrine\Entities\BaseEntity
{

	use Doctrine\Entities\Attributes\Identifier;

	/** @ORM\Column(type="string", name="`key`", unique=TRUE) */
	protected $key;

	/** @ORM\Column(type="string", name="`value`", nullable=TRUE) */
	protected $value;

}
