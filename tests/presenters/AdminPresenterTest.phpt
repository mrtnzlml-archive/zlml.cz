<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * Class AdminPresenterTest
 * @package Test
 * @skip
 */
class AdminPresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
	}

	public function setUp() {
		$this->tester->init('Admin');
		$this->tester->logIn(TRUE, 'Sign');
	}

	public function testRenderDefault() {
		$this->tester->testAction('default');
	}

}

$test = new AdminPresenterTest($container);
$test->run();