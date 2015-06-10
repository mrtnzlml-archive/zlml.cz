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
		$this->checkAction('default', 'GET', ['search' => 'nette']);
	}

	public function testRenderDefaultEmpty()
	{
		$this->checkAction('default', 'GET', ['search' => 'pritomtodotazupravdepodobnenicvdatabazinenajdu']);
	}

	public function testSearchForm()
	{
		$this->checkForm('default', 'search', [
			'search' => 'test',
		]);
		$this->checkForm('default', 'search', [
			'search' => 'ač óR ůz',
		]);
	}

}

(new SearchPresenterTest)->run();
