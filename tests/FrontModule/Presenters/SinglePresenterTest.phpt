<?php declare(strict_types = 1);

namespace App\Tests\FrontModule\Presenters;

use App\Posts\Posts;
use Nette;
use Tester;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class SinglePresenterTest extends \Tester\TestCase
{

	use \Testbench\TCompiledContainer;
	use \Testbench\TPresenter;

	/** @var Posts */
	private $posts;

	public function __construct()
	{
		$this->posts = $this->getService(Posts::class);
	}

	public function testRenderAbout()
	{
		$this->checkAction('Front:Single:about');
	}

	/** @dataProvider dataArticles */
	public function testRenderArticles($slug)
	{
		$this->checkAction('Front:Single:article', ['slug' => $slug]);
	}

	public function testRedirectEmptyArticle()
	{
		$this->checkRedirect('Front:Single:article', '/');
	}

	public function testNonWebalizedArticle()
	{
		$this->checkRedirect('Front:Single:article', '/rcs-rcs-5', ['slug' => 'rčš .rčš 5']);
	}

	public function testForward()
	{
		/** @var Nette\Application\Responses\ForwardResponse $response */
		$response = $this->check('Front:Single:article', ['slug' => 'about']);
		Tester\Assert::true($response instanceof Nette\Application\Responses\ForwardResponse);
		//TODO: checkForward method in testbench...
	}

	public function testRenderObsah()
	{
		$this->checkAction('Front:Single:obsah');
	}

	public function testRenderReference()
	{
		$this->checkAction('Front:Single:reference');
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
