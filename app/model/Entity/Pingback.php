<?php declare(strict_types=1);

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="pingbacks")
 */
class Pingback extends Doctrine\Entities\BaseEntity
{

	use Doctrine\Entities\Attributes\Identifier;

}
