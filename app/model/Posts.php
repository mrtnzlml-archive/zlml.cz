<?php

namespace Model;

use Doctrine;
use Kdyby;
use Nette\Utils\Strings;
use Nette;

/**
 * TODO: QueryObject
 * Class Posts
 * @package Model
 */
class Posts extends Nette\Object {

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
	 * @param null $limit
	 * @param null $offset
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		$query = $this->dao->createQueryBuilder('p')
			->whereCriteria($criteria)
			->autoJoinOrderBy((array)$orderBy)
			->leftJoin('p.tags', 'tt') //t already used?
			->addSelect('tt')
			->getQuery();
		$resultSet = new Kdyby\Doctrine\ResultSet($query);
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
		$posts = iterator_to_array($this->findBy(array('publish_date <=' => new \DateTime())));
		return $posts[rand(0, count($posts) - 1)];
	}

	/**
	 * @param $search
	 * @return array
	 */
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
		$rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('title_score', 'title_score');
		$rsm->addScalarResult('body_score', 'body_score');
		$sql = "SELECT id, 5 * MATCH(title) AGAINST (?) AS title_score, MATCH(body) AGAINST (?) AS body_score
				FROM mirror_posts WHERE MATCH(title, body) AGAINST(? IN BOOLEAN MODE)$where
				ORDER BY 5 * MATCH(title) AGAINST (?) + MATCH(body) AGAINST (?) DESC";
		$query = $em->createNativeQuery($sql, $rsm);
		$query->setParameters(array($search, $search, $search, $search, $search));
		$result = $query->getScalarResult();
		$ids = array_map('current', $result);
		//FIXME:WARNING: temporary ugly hack because WHERE id IN (79, 10, 45, 54, 62) doesn't keep order
		$tmp = [];
		foreach ($ids as $key => $value) {
			$relevance = $result[$key]['title_score'];
			$article = $this->findOneBy(['id' => $value, 'publish_date <=' => new \DateTime()]);
			if (empty($article)) {
				continue;
			}
			$tmp[$key . '#' . $relevance] = $article;
		}
		return $tmp;
		//return $this->findBy(array('id' => $ids));
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

}
