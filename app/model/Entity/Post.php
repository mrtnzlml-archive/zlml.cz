<?php

namespace Entity;

use App;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post extends Doctrine\Entities\BaseEntity {

	/**
	 * @ORM\ManyToMany(targetEntity="Tag", inversedBy="posts", cascade={"persist"})
	 * @ORM\JoinTable(name="posts_tags")
	 * @ORM\OrderBy({"name" = "ASC"})
	 */
	protected $tags;

	public function __construct() {
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function addTag(Tag $newTag) {
		$add = TRUE;
		foreach ($this->tags as $tag) {
			if ($tag->name == $newTag->name) {
				$add = FALSE;
				break;
			}
		}
		if ($add) {
			$this->tags->add($newTag);
		}
	}

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

	/** @ORM\Column(type="datetime") */
	public $publish_date;

	/** @ORM\Column(type="boolean") */
	public $draft = FALSE; //TRUE;

}
