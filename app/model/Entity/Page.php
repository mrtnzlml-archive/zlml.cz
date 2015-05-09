<?php

namespace Entity;

use App;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="pages")
 */
class Page extends Doctrine\Entities\BaseEntity
{

	use Doctrine\Entities\Attributes\Identifier;

	/** @ORM\Column(type="text") */
	protected $title;

	/** @ORM\Column(type="string", unique=TRUE) */
	protected $slug;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\Column(type="datetime") */
	protected $date;

	/** @ORM\Column(type="boolean") */
	protected $draft = FALSE; //TRUE;

}
