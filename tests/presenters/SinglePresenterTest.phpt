<?php

namespace Test;

use App;
use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

class SinglePresenterTest extends Tester\TestCase {

	/** @var App\Posts */
	private $posts;

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
		$this->posts = $container->getByType('\App\Posts');
	}

	public function setUp() {
		$this->tester->init('Single');
	}

	public function testRenderAbout() {
		$this->tester->testAction('about');
	}

	/** @dataProvider dataArticles */
	public function testRenderArticles($slug) {
		$this->tester->testAction('article', 'GET', array('slug' => $slug));
	}

	public function testRedirectEmptyArticle() {
		$response = $this->tester->test('article');
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

	public function testRenderObsah() {
		$this->tester->testAction('obsah');
	}

	public function testRenderReference() {
		$this->tester->testAction('reference');
	}

	///// dataProviders /////

	/**
	 * @return array of arrays
	 */
	public function dataArticles() {
		$articles = $this->posts->findBy([], NULL, 10, 0);
		//$articles = $this->posts->findBy([]);
		//$articles = $this->posts->findOneBy([]);
		$data = array();
		foreach ($articles as $article) {
			$data[] = array($article->slug);
		}
		return $data;
	}

}

$test = new SinglePresenterTest($container);
$test->run();