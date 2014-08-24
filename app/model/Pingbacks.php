<?php

namespace Model;

use Doctrine;
use Kdyby;
use Nette;

/**
 * Class Pingbacks
 * @package Model
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