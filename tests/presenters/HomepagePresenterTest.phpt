<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HomepagePresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new PresenterTester($container, 'Homepage');
	}

	public function testRenderDefault() {
		$this->tester->testAction('default');
	}

	public function testRenderDefaultPage2() {
		$this->tester->testAction('default', Presenter::GET, array(
			'paginator-page' => 2,
		));
	}

	public function testRenderRss() {
		$response = $this->tester->test('rss');
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);
		$html = (string)$response->getSource();
		$dom = @Tester\DomQuery::fromHtml($html);
		Tester\Assert::true($dom->has('rss'));
		Tester\Assert::true($dom->has('channel'));
		Tester\Assert::true($dom->has('title'));
		Tester\Assert::true($dom->has('link'));
		Tester\Assert::true($dom->has('description'));
		Tester\Assert::true($dom->has('language'));
		Tester\Assert::true($dom->has('item'));
	}

	public function testRenderSitemap() {
		$response = $this->tester->test('sitemap');
		Tester\Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Tester\Assert::true($response->getSource() instanceof Nette\Application\UI\ITemplate);
		$html = (string)$response->getSource();
		$dom = @Tester\DomQuery::fromHtml($html);
		Tester\Assert::true($dom->has('urlset'));
		Tester\Assert::true($dom->has('url'));
		Tester\Assert::true($dom->has('loc'));
	}

}

$test = new HomepagePresenterTest($container);
$test->run();