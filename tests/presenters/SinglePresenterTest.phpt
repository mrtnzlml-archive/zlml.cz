<?php declare(strict_types=1);

namespace Test;

use Model;
use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SinglePresenterTest extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;
	use \Testbench\TPresenter;

	/** @var Model\Posts */
	private $posts;

	public function __construct()
	{
		$this->posts = $this->getService(Model\Posts::class);
	}

	public function testRenderAbout()
	{
		$this->checkAction('Single:about');
	}

	/** @dataProvider dataArticles */
	public function testRenderArticles($slug)
	{
		$this->checkAction('Single:article', ['slug' => $slug]);
	}

	public function testRedirectEmptyArticle()
	{
		$this->checkRedirect('Single:article', '/');
	}

	public function testNonWebalizedArticle()
	{
		$this->checkRedirect('Single:article', '/rcs-rcs-5', ['slug' => 'rčš .rčš 5']);
	}

	public function testForward()
	{
		/** @var Nette\Application\Responses\ForwardResponse $response */
		$response = $this->check('Single:article', ['slug' => 'about']);
		Tester\Assert::true($response instanceof Nette\Application\Responses\ForwardResponse);
		//TODO: checkForward method in testbench...
	}

	public function testRenderObsah()
	{
		$this->checkAction('Single:obsah');
	}

	public function testRenderReference()
	{
		$this->checkAction('Single:reference');
	}

	///// dataProviders /////

	/**
	 * @return array of arrays
	 */
	public function dataArticles()
	{
		$articles = $this->posts->findBy([], NULL, 10, 0);
		//$articles = $this->posts->findBy(array());
		//$articles = $this->posts->findOneBy(array());
		$data = [];
		foreach ($articles as $article) {
			$data[] = [$article->slug];
		}
		return $data;
	}

}

(new SinglePresenterTest)->run();
