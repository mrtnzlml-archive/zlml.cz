<?php

namespace Entity;

use App;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="mirror_posts",
 *      options={"engine"="MyISAM"},
 *      indexes={
 *          @ORM\Index(columns={"title, body"}, flags={"fulltext"}),
 *          @ORM\Index(columns={"title"}, flags={"fulltext"}),
 *          @ORM\Index(columns={"body"}, flags={"fulltext"})
 *      }
 * )
 * TODO: flags feature is available in Doctrine 2.5
 * see: https://github.com/doctrine/doctrine2/commit/3a1e24e6801961128c27104919050d40d745030b
 */
class PostMirror extends Doctrine\Entities\BaseEntity {

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
