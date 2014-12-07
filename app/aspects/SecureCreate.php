<?php

namespace Secure;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Create extends Annotation {

	/**
	 * @var string
	 */
	public $allow;

}
