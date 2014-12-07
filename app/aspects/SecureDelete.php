<?php

namespace Secure;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Delete extends Annotation {

	/**
	 * @var string
	 */
	public $allow;

}
