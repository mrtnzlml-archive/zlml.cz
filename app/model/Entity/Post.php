<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts", cascade={"persist", "remove"})
	 * @ORM\JoinTable(name="posts_tags")
	 */
	protected $tags;

	public function __construct() {
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function addTag(Tag $tag) {
		//FIXME: přidávají se i duplicity
		//$this->tags[] = $tag;
		$this->tags->add($tag);
	}

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

	/** @ORM\Column(type="datetime") */
	protected $release_date;

}