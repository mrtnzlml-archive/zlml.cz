<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 */
class Tag extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags")
	 * @ORM\OrderBy({"date" = "DESC"})
	 **/
	protected $posts;

	public function __construct() {
		$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="string", length=50) */
	protected $name;

	/** @ORM\Column(type="string", length=6) */
	protected $color;

}