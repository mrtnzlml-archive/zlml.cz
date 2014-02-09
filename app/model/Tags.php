<?php

namespace Model;

use Nette;

class Tags extends Nette\Object {

	/** @var \Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $context) {
		$this->database = $context;
	}

	public function getByName($name) {
		return $this->database->table('tags')->where('name = ?', $name);
	}

	public function getAllTags() {
		return $this->database->table('tags');
	}

}