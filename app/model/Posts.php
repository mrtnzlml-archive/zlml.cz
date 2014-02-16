<?php

namespace App;

use Doctrine;
use Kdyby;
use Nette;

/**
 * Class Posts
 * @package App
 */
class Posts extends Nette\Object {

	/** @var \Kdyby\Doctrine\EntityDao */
	private $dao;

	/**
	 * @param Kdyby\Doctrine\EntityDao $dao
	 */
	public function __construct(Kdyby\Doctrine\EntityDao $dao) {
		$this->dao = $dao;
	}

	public function save($entity = NULL, $relations = NULL) {
		return $this->dao->save($entity, $relations);
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
	 * @param array $criteria
	 * @param array $orderBy
	 * @return mixed|null|object
	 */
	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->dao->findOneBy($criteria, $orderBy);
	}

	/**
	 * @param array $criteria
	 * @return mixed
	 */
	public function countBy(array $criteria = array()) {
		return $this->dao->countBy($criteria);
	}

	/**
	 * @param \DateTime $date
	 * @return mixed
	 */
	public function findOlder(\DateTime $date) {
		$query = $this->dao->select()->where('? > date', $date)->order('date DESC')->limit(1);
		try {
			return $query->createQuery()->getSingleResult();
		} catch (Doctrine\ORM\NoResultException $exc) {
			return NULL;
		}
	}

	/**
	 * @param \DateTime $date
	 * @return mixed
	 */
	public function findNewer(\DateTime $date) {
		$query = $this->dao->select()->where('? < date', $date)->limit(1);
		try {
			return $query->createQuery()->getSingleResult();
		} catch (Doctrine\ORM\NoResultException $exc) {
			return NULL;
		}
	}

	/**
	 * @return mixed|null
	 */
	public function rand() {
		$posts = $this->findBy(array());
		return $posts[rand(0, count($posts) - 1)];
	}

	//FIXME: remove?
	public function __call($method, $arguments) {
		return $this->dao->$method($arguments);
	}

}