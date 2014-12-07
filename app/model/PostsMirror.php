<?php

namespace Model;

use Doctrine;
use Entity;
use Kdyby;
use Nette;

/**
 * Class PostsMirror
 * @package Model
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
	 * @param array $criteria
	 * @param array $orderBy
	 * @return mixed|null|object
	 *
	 * @Secure\Read(allow="guest")
	 */
	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->dao->findOneBy($criteria, $orderBy);
	}

	/**
	 * @param null $entity
	 * @param null $relations
	 * @return array
	 *
	 * @Secure\Create(allow="admin")
	 * @Secure\Update(allow="admin")
	 */
	public function save($entity = NULL, $relations = NULL) {
		return $this->dao->save($entity, $relations);
	}

	/**
	 * @param $entity
	 * @param null $relations
	 * @param bool $flush
	 *
	 * @Secure\Delete(allow="admin")
	 */
	public function delete($entity, $relations = NULL, $flush = Kdyby\Persistence\ObjectDao::FLUSH) {
		$this->dao->delete($entity, $relations, $flush);
	}

}
