<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SearchPresenterTest extends Tester\TestCase
{

	public function __construct(Nette\DI\Container $container)
	{
		$this->tester = new PresenterTester($container, 'Search');
	}

	public function testRenderDefault()
	{
		$this->tester->testAction('default', 'GET', ['search' => 'nette']);
	}

	public function testRenderDefaultEmpty()
	{
		$this->tester->testAction('default', 'GET', ['search' => 'pritomtodotazupravdepodobnenicvdatabazinenajdu']);
	}

	public function testSearchForm()
	{
		$response = $this->tester->test('default', 'POST', [
			'do' => 'search-submit',
		], [
			'search' => 'test',
		]);
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
		$response = $this->tester->test('default', 'POST', [
			'do' => 'search-submit',
		], [
			'search' => 'ač óR ůz',
		]);
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

}

$test = new SearchPresenterTest($container);
$test->run();
