<?php

namespace Secure;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Reads extends Annotation {

	/**
	 * @var string
	 */
	public $allow;

}
