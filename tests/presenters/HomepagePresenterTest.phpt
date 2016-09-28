<?php declare(strict_types = 1);

namespace Test;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HomepagePresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Homepage:default');
	}

	public function testRenderDefaultPage2()
	{
		$this->checkAction('Homepage:default', [
			'paginator-page' => 2,
		]);
	}

	public function testRenderRss()
	{
		$this->checkRss('Homepage:rss');
	}

	public function testRenderSitemap()
	{
		$this->checkSitemap('Homepage:sitemap');
	}

}

(new HomepagePresenterTest)->run();
