<?php

namespace App;
use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory {

	private $posts;

	public function __construct(\Model\Posts $posts) {
		$this->posts = $posts;
	}

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter() {
		$router = new RouteList();
		$router[] = new Route('index.php', 'Homepage:default', Route::ONE_WAY);
		$router[] = new Route('sitemap.xml', 'Homepage:sitemap');
		$router[] = new Route('[<paginator-page [0-9]+>]', 'Homepage:default');

		//TODO: toto není moc OK (ale funkční)
		$router[] = new Route('<id>', array(
			'presenter' => 'Single',
			'action' => 'article',
			'id' => array(
				Route::FILTER_IN => function ($url) {
					return $this->posts->getIdByUrl($url);
				},
				Route::FILTER_OUT => function ($id) {
					return $this->posts->getUrlById($id);
				},
			),
		));

		$router[] = new Route('search[/<search>]', 'Search:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
