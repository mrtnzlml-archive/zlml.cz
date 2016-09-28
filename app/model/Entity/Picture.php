<?php declare(strict_types=1);

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="pictures")
 *
 * @method setUuid(string)
 * @method setName(string)
 */
class Picture extends Doctrine\Entities\BaseEntity
{

	use Doctrine\Entities\Attributes\Identifier;

	/** @ORM\Column(type="string", length=40) */
	protected $uuid;

	/** @ORM\Column(type="string", length=255) */
	protected $name;

	/** @ORM\Column(type="datetime") */
	protected $created;

}
