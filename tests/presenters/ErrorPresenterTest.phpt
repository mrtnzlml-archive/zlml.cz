<?php

namespace Test;

use Nette;
use Tester;
use Tracy;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class ErrorPresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new PresenterTester($container);
	}

	public function setUp() {
		Tracy\Debugger::$productionMode = TRUE;
		$this->tester->init('Error');
	}

	public function testRenderDefault() {
		$response = $this->tester->test('default');
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);
	}

	/*public function testRender404() {
		//TODO: howto?
	}*/

}

$test = new ErrorPresenterTest($container);
$test->run();
