<?php

namespace App;

use Doctrine;
use Kdyby;
use Nette;
use Nette\Utils\Strings;

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

	/**
	 * @param null $entity
	 * @param null $relations
	 * @return array
	 */
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
		$query = $this->dao->createQueryBuilder('p')
			->whereCriteria($criteria)
			->autoJoinOrderBy((array)$orderBy)
			->join('p.tags', 'tt') //t already used?
			->addSelect('tt')
			->getQuery();
		$resultSet = new Kdyby\Doctrine\ResultSet($query);
		$resultSet->setFetchJoinCollection(FALSE); //generate less db queries, try it!
		return $resultSet->applyPaging($offset, $limit)->getIterator();
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
		$posts = iterator_to_array($this->findBy([]));
		return $posts[rand(0, count($posts) - 1)];
	}

	public function fulltextSearch($search) {
		$search = Strings::lower(Strings::normalize($search));
		$search = Strings::replace($search, '/[^\d\w]/u', ' ');
		$words = Strings::split(Strings::trim($search), '/\s+/u');
		$words = array_unique(array_filter($words, function ($word) {
			return Strings::length($word) > 1;
		}));
		$words = array_map(function ($word) {
			return Strings::toAscii($word) . '*';
		}, $words);
		$search = implode(' ', $words);

		$where = "";
		$ft_min_word_len = 4;
		preg_match_all("~[\\pL\\pN_]+('[\\pL\\pN_]+)*~u", stripslashes($search), $matches);
		foreach ($matches[0] as $part) {
			if (iconv_strlen($part, "utf-8") < $ft_min_word_len) {
				$accents = array('aá', 'cč', 'dď', 'eéě', 'ií', 'nň', 'oó', 'rř', 'sš', 'tť', 'uúů', 'yý', 'zž');
				foreach ($accents as $accent) {
					$part = preg_replace("<[$accent]>iu", "[$accent]+", $part);
				}
				$regexp = "REGEXP '[[:<:]]" . addslashes(mb_strtoupper($part, 'UTF-8')) . "[[:>:]]'";
				$where .= " OR (title $regexp OR body $regexp)";
			}
		}

		$em = $this->dao->getEntityManager();
		$rsm = new Doctrine\ORM\Query\ResultSetMapping();
		$rsm->addEntityResult('\Entity\Post', 'u');
		$rsm->addFieldResult('u', 'id', 'id');
		$sql = "SELECT u.id FROM mirror_posts u WHERE MATCH(u.title, u.body) AGAINST(? IN BOOLEAN MODE)$where
				ORDER BY 5 * MATCH(u.title) AGAINST (?) + MATCH(u.body) AGAINST (?) DESC";
		$query = $em->createNativeQuery($sql, $rsm);
		$query->setParameters([$search, $search, $search]);
		$result = $query->getScalarResult();
		$ids = array_map('current', $result);
		return $this->findBy(['id' => $ids]);
	}

	/**
	 * @param $entity
	 * @param null $relations
	 * @param bool $flush
	 */
	public function delete($entity, $relations = NULL, $flush = Kdyby\Persistence\ObjectDao::FLUSH) {
		$this->dao->delete($entity, $relations, $flush);
	}

}