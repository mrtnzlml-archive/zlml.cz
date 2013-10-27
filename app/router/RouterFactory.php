<?php

namespace App;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette;


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
		define('ITEMCOUNT', $this->posts->getAllPosts()->select('COUNT(*) AS itemCount')->fetch()->itemCount);
		$pages = ITEMCOUNT;
		$range = range(1, ceil($pages / 8));
		$paginator = implode('|', $range);

		$router = new RouteList();
		$router[] = new Route('rss', 'Homepage:rss');
		$router[] = new Route('sitemap.xml', 'Homepage:sitemap');
		$router[] = new Route('admin', 'Admin:default');
		$router[] = new Route("[<paginator-page [$paginator]>]", array(
			'presenter' => 'Homepage',
			'action' => 'default',
			'paginator-page' => 1
		));

		$router[] = new Route('<slug>', 'Single:article');
		$router[] = new Route('<action>', 'Single:article');

		$router[] = new Route('s[/<search>]', 'Search:default');
		$router[] = new Route('t[/<search>]', 'Tag:default');

		$router[] = new Route('search[/<search>]', 'Search:default', Route::ONE_WAY);
		$router[] = new Route('tag[/<search>]', 'Tag:default', Route::ONE_WAY);

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
