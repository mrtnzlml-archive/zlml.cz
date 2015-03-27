<?php

namespace Entity;

use App;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="pages")
 */
class Page extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="text") */
	protected $title;

	/** @ORM\Column(type="text") */
	protected $slug;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\Column(type="datetime") */
	protected $date;

	/** @ORM\Column(type="boolean") */
	protected $draft = FALSE; //TRUE;

}
