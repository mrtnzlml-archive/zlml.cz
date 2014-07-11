<?php

namespace App;

use Doctrine;
use Kdyby;
use Nette;

/**
 * Class PostsMirror
 * @package App
 */
class PostsMirror extends Nette\Object {

	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;

	/**
	 * @param Kdyby\Doctrine\EntityDao $dao
	 */
	public function __construct(Kdyby\Doctrine\EntityDao $dao) {
		$this->dao = $dao;
	}

	/**
	 * @param null $entity
	 * @param null $relations
	 * @return array
	 */
	public function save($entity = NULL, $relations = NULL) {
		return $this->dao->save($entity, $relations);
	}

	public function delete($entity, $relations = NULL, $flush = Kdyby\Persistence\ObjectDao::FLUSH) {
		$this->dao->delete($entity, $relations, $flush);
	}

}
