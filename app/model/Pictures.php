<?php declare(strict_types = 1);

namespace Model;

use Doctrine;
use Kdyby;
use Nette;

class Pictures extends Nette\Object
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
	 * @return array
	 */
	public function save($entity = NULL, $relations = NULL)
	{
		return $this->dao->save($entity, $relations);
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

	public function delete($entity, $relations = NULL, $flush = Kdyby\Persistence\ObjectDao::FLUSH)
	{
		$this->dao->delete($entity, $relations, $flush);
	}

	/**
	 * @return mixed
	 */
	public function countBy(array $criteria = [])
	{
		return $this->dao->countBy($criteria);
	}

}
