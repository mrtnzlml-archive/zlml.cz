<?php

namespace App;

use Doctrine;
use Kdyby;
use Nette;

/**
 * Class Pingbacks
 * @package App
 */
class Pingbacks extends Nette\Object {

	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;

	/**
	 * @param Kdyby\Doctrine\EntityDao $dao
	 */
	public function __construct(Kdyby\Doctrine\EntityDao $dao) {
		$this->dao = $dao;
	}

}