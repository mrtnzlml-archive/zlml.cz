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
	 * @param null $entity
	 * @param null $relations
	 * @return array
	 */
	public function save($entity = NULL, $relations = NULL) {
		if ($entity instanceof Entity\Post) {
			$postsMirror = new Entity\PostMirror;
			$postsMirror->title = $entity->title;
			$postsMirror->body = $entity->body;
			$postsMirror->date = $entity->date;
		} else {
			$postsMirror = $entity;
		}
		return $this->dao->save($postsMirror, $relations);
	}

	public function delete($entity, $relations = NULL, $flush = Kdyby\Persistence\ObjectDao::FLUSH) {
		$this->dao->delete($entity, $relations, $flush);
	}

}
