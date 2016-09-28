<?php declare(strict_types = 1);

namespace App\Tests\FrontModule\Presenters;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class HomepagePresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Front:Homepage:default');
	}

	public function testRenderDefaultPage2()
	{
		$this->checkAction('Front:Homepage:default', [
			'paginator-page' => 2,
		]);
	}

	public function testRenderRss()
	{
		$this->checkRss('Front:Homepage:rss');
	}

	public function testRenderSitemap()
	{
		$this->checkSitemap('Front:Homepage:sitemap');
	}

}

(new HomepagePresenterTest)->run();
