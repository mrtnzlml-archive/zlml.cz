<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

class TagPresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
	}

	public function setUp() {
		$this->tester->init('Tag');
	}

	public function testRenderDefault() {
		$this->tester->testAction('default');
	}

}

id(new TagPresenterTest($container))->run();