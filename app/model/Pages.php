<?php

namespace Model;

use Doctrine;
use Kdyby;
use Nette;

/**
 * Class Pages
 * @package Model
 */
class Pages extends Nette\Object {

	public $onSave = [];
	public $onDelete = [];

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
		$entity = $this->dao->save($entity, $relations);
		$this->onSave($entity);
		return $entity;
	}

	/**
	 * @param array $criteria
	 * @param array $orderBy
	 * @return mixed|null|object
	 */
	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->dao->findOneBy($criteria, $orderBy);
	}

	/**
	 * @param array $criteria
	 * @param array $orderBy
	 * @param null $limit
	 * @param null $offset
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
	}

	/**
	 * @param $entity
	 * @param null $relations
	 * @param bool $flush
	 */
	public function delete($entity, $relations = NULL, $flush = Kdyby\Persistence\ObjectDao::FLUSH) {
		$this->dao->delete($entity, $relations, $flush);
		$this->onDelete($entity);
	}

	/**
	 * @param array $criteria
	 * @return mixed
	 */
	public function countBy(array $criteria = []) {
		return $this->dao->countBy($criteria);
	}

}
