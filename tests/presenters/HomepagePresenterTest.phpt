<?php

namespace Test;

use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HomepagePresenterTest extends \CustomTestCase
{

	public function setUp()
	{
		$this->openPresenter('Homepage:');
	}

	public function testRenderDefault()
	{
		$this->checkAction('default');
	}

	public function testRenderDefaultPage2()
	{
		$this->checkAction('default', 'GET', [
			'paginator-page' => 2,
		]);
	}

	public function testRenderRss()
	{
		$this->checkRss('rss');
	}

	public function testRenderSitemap()
	{
		$this->checkSitemap('sitemap');
	}

}

(new HomepagePresenterTest)->run();
