<?php

namespace Model;

use Nette;

class Tags extends Nette\Object {

	/** @var Nette\Database\Context @inject */
	public $database;

	public function getByName($name) {
		return $this->database->table('tags')->where('name = ?', $name);
	}

	public function getAllTags() {
		return $this->database->table('tags');
	}

}