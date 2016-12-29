<?php declare(strict_types = 1);

namespace App\Tests\FrontModule\Presenters;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class SearchPresenterTest extends \Tester\TestCase
{

	use \Testbench\TPresenter;

	public function testRenderDefault()
	{
		$this->checkAction('Front:Search:default', ['search' => 'nette']);
	}

	public function testRenderDefaultEmpty()
	{
		$this->checkAction('Front:Search:default', ['search' => 'pritomtodotazupravdepodobnenicvdatabazinenajdu']);
	}

}

(new SearchPresenterTest)->run();
