<?php

namespace Secure;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Read extends Annotation {

	/**
	 * @var string
	 */
	public $allow;

}
