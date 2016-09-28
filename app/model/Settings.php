<?php declare(strict_types = 1);

namespace Model;

use Entity;
use Kdyby;
use Nette;

class Settings extends Nette\Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	private $dao;

	/**
	 * @param Kdyby\Doctrine\EntityDao $dao
	 */
	public function __construct(Kdyby\Doctrine\EntityDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * @param Nette\Utils\ArrayHash $vals
	 */
	public function save(Nette\Utils\ArrayHash $vals)
	{
		foreach ($vals as $key => $value) {
			$entity = $this->findOneBy(['key' => $key]);
			if ($entity) {
				if ($entity->value != $value) {
					$entity->value = $value;
					$this->dao->add($entity);
				}
			} else {
				$entity = new Entity\Setting;
				$entity->key = $key;
				$entity->value = $value;
				$this->dao->add($entity);
			}
		}
		$em = $this->dao->getEntityManager();
		$em->flush();
	}

	/**
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
	}

	/**
	 * @return mixed|null|object
	 */
	public function findOneBy(array $criteria, array $orderBy = NULL)
	{
		return $this->dao->findOneBy($criteria, $orderBy);
	}

	/**
	 * @return array
	 */
	public function findByKeys(array $keys)
	{
		$keys = $this->dao->findBy(['key' => $keys]);
		$result = [];
		foreach ($keys as $key) {
			$result[$key->key] = is_numeric($key->value) ? (float)$key->value : $key->value;
		}
		return $result;
	}

	/**
	 * @return Nette\Utils\ArrayHash
	 */
	public function findAllByKeys()
	{
		$keys = $this->dao->findBy([]);
		$result = [];
		foreach ($keys as $key) {
			$result[$key->key] = is_numeric($key->value) ? (float)$key->value : $key->value;
		}
		return Nette\Utils\ArrayHash::from($result);
	}

	/**
	 * @return int|null|string
	 */
	public function findOneByKey($key)
	{
		$result = $this->dao->findOneBy(['key' => $key]);
		if ($result) {
			$result = $result->value;
			return is_numeric($result) ? $result + 0 : $result; // int | double | string
		} else {
			return NULL;
		}
	}

}
