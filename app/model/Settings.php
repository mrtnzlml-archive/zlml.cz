<?php

namespace Model;

use Kdyby;
use Nette;

/**
 * Class Settings
 * @package Model
 */
class Settings extends Nette\Object {

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
	 * @param array $keys
	 * @return array
	 */
	public function findByKeys(array $keys) {
		$keys = $this->dao->findBy(['key' => $keys]);
		$result = array();
		foreach ($keys as $key) {
			$result[$key->key] = is_numeric($key->value) ? (float)$key->value : $key->value;
		}
		return $result;
	}

	/**
	 * @param $key
	 * @return int|null|string
	 */
	public function findOneByKey($key) {
		$result = $this->dao->findOneBy(['key' => $key]);
		if ($result) {
			$result = $result->value;
			return is_numeric($result) ? $result + 0 : $result; // int | double | string
		} else {
			return NULL;
		}
	}

}
