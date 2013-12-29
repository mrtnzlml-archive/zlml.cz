<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

class SinglePresenterTest extends Tester\TestCase {

	/** @var \Model\Posts */
	private $posts;

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
		$this->posts = $container->createInstance('\Model\Posts');
	}

	public function setUp() {
		$this->tester->init('Single');
	}

	public function testRenderAbout() {
		$this->tester->testAction('about');
	}

	///** @dataProvider dataArticles */
	//FIXME: no idea how to get model classes...
	/*public function testRenderArticles($slug) {
		$this->tester->testAction('article', 'GET', array('slug' => $slug));
	}*/

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
		$articles = $this->posts->getAllPosts();
		$data = array();
		foreach ($articles as $article) {
			$data[] = array($article->slug);
		}
		return $data;
	}

}

id(new SinglePresenterTest($container))->run();