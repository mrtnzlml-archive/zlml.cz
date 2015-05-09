<?php

namespace Secure;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Update extends Annotation
{

	/**
	 * @var string
	 */
	public $allow;

}
