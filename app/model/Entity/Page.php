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
	public $title;

	/** @ORM\Column(type="text") */
	public $slug;

	/** @ORM\Column(type="text") */
	public $body;

	/** @ORM\Column(type="datetime") */
	public $date;

	/** @ORM\Column(type="boolean") */
	public $draft = FALSE; //TRUE;

}
