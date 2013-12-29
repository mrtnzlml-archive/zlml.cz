<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

class SearchPresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
	}

	public function setUp() {
		$this->tester->init('Search');
	}

	public function testRenderDefault() {
		$this->tester->testAction('default');
	}

}

id(new SearchPresenterTest($container))->run();