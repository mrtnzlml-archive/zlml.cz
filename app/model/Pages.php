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
	 * @Secure\Reads(allow="guest")
	 */
	public function countBy(array $criteria = []) {
		return $this->dao->countBy($criteria);
	}

}
