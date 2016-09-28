<?php declare(strict_types = 1);

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 *
 * @method setName(string)
 * @method setColor(string)
 */
class Tag extends Doctrine\Entities\BaseEntity
{

	use Doctrine\Entities\Attributes\Identifier;

	/**
	 * @ORM\ManyToMany(targetEntity="Post", mappedBy="tags")
	 * @ORM\OrderBy({"date" = "DESC"})
	 **/
	protected $posts;

	public function __construct()
	{
		$this->posts = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/** @ORM\Column(type="string", length=50) */
	protected $name;

	/** @ORM\Column(type="string", length=6) */
	protected $color;

}
