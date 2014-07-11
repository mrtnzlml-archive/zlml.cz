<?php

namespace Entity;

use App;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="mirror_posts", options={"engine"="MyISAM"})
 */
class PostMirror extends Doctrine\Entities\BaseEntity {

	/* TODO: MyISAM
		PRIMARY	id
		FULLTEXT	title, body
		FULLTEXT	title
		FULLTEXT	body
	 */

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** @ORM\Column(type="text") */
	protected $title;

	/** @ORM\Column(type="text") */
	protected $body;

	/** @ORM\Column(type="datetime") */
	protected $date;

}
