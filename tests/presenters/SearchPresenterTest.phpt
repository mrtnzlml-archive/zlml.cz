<?php

namespace Test;

use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SearchPresenterTest extends \CustomTestCase
{

	public function setUp()
	{
		$this->openPresenter('Search:');
	}

	public function testRenderDefault()
	{
		$this->checkAction('default', ['search' => 'nette']);
	}

	public function testRenderDefaultEmpty()
	{
		$this->checkAction('default', ['search' => 'pritomtodotazupravdepodobnenicvdatabazinenajdu']);
	}

	public function testSearchForm()
	{
//		FIXME:
		$this->checkForm('default', 'search', [
			'search' => 'test',
		]);
//		$this->checkRedirect('default', '/s/test', 'POST', [
//			'do' => 'search-submit',
//		], [
//			'search' => 'test',
//		]);
//		FIXME:
		$this->checkForm('default', 'search', [
			'search' => 'ač óR ůz',
		]);
	}

}

(new SearchPresenterTest)->run();
