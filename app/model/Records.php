<?php

namespace Model;

use Nette;

/**
 * Class Records
 * @package Model
 */
class Records extends Nette\Object {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;

	public function newRecord($data) {
		$this->sf->table('records')->insert($data);
	}

	public function getRecords() {
		return $this->sf->table('records');
	}

}