<?php declare(strict_types=1);

namespace Test;

use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SearchPresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Search:default', ['search' => 'nette']);
	}

	public function testRenderDefaultEmpty()
	{
		$this->checkAction('Search:default', ['search' => 'pritomtodotazupravdepodobnenicvdatabazinenajdu']);
	}

	public function testSearchForm()
	{
		$this->checkForm('Search:default', 'search', [
			'search' => 'test',
		], '/s/test');

//		$this->checkForm('Search:default', 'search', [
//			'search' => 'ač óR ůz',
//		], '/s/ač%20óR%20ůz');
	}

}

(new SearchPresenterTest)->run();
