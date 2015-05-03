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

	use Doctrine\Entities\Attributes\Identifier;

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

	/** @ORM\Column(type="text") */
	protected $title;

	/** @ORM\Column(type="string", unique=TRUE) */
	protected $slug;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\Column(type="datetime") */
	protected $date;

	/** @ORM\Column(type="datetime") */
	protected $publish_date;

	/** @ORM\Column(type="boolean") */
	protected $disable_comments = FALSE;

	/** @ORM\Column(type="boolean") */
	protected $draft = FALSE; //TRUE;

}
