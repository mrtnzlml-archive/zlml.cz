<?php

namespace Test;

use Model;
use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SinglePresenterTest extends Tester\TestCase {

	/** @var Model\Posts */
	private $posts;

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new PresenterTester($container, 'Single');
		$this->posts = $container->getByType('\Model\Posts');
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

	public function testNonWebalizedArticle() {
		$response = $this->tester->test('article', 'GET', array('slug' => 'rčš .rčš 5'));
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
	}

	public function testForward() {
		$response = $this->tester->test('article', 'GET', array('slug' => 'about'));
		Tester\Assert::true($response instanceof Nette\Application\Responses\ForwardResponse);
	}

	public function testRenderDevelop() {
		$this->tester->testAction('develop');
	}

	public function testRenderObsah() {
		$this->tester->testAction('obsah');
	}

	public function testRenderReference() {
		$this->tester->testAction('reference');
	}

	public function testRandom() {
		$response = $this->tester->test('default', 'GET', array('do' => 'random'));
		Tester\Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);

	}

	///// dataProviders /////

	/**
	 * @return array of arrays
	 */
	public function dataArticles() {
		$articles = $this->posts->findBy(array(), NULL, 10, 0);
		//$articles = $this->posts->findBy(array());
		//$articles = $this->posts->findOneBy(array());
		$data = array();
		foreach ($articles as $article) {
			$data[] = array($article->slug);
		}
		return $data;
	}

}

$test = new SinglePresenterTest($container);
$test->run();
