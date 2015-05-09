<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HomepagePresenterTest extends Tester\TestCase
{

	public function __construct(Nette\DI\Container $container)
	{
		$this->tester = new PresenterTester($container, 'Homepage');
	}

	public function testRenderDefault()
	{
		$this->tester->testAction('default');
	}

	public function testRenderDefaultPage2()
	{
		$this->tester->testAction('default', PresenterTester::GET, [
			'paginator-page' => 2,
		]);
	}

	public function testRenderRss()
	{
		$this->tester->testRss('rss');
	}

	public function testRenderSitemap()
	{
		$this->tester->testSitemap('sitemap');
	}

}

$test = new HomepagePresenterTest($container);
$test->run();
