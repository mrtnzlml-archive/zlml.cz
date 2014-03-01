<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Router factory.
 */
class RouterFactory {

	private $posts;

	private $broken_links = array(
		//broken => repaired
		'feed' => 'Homepage:rss',
	);

	public function __construct(Posts $posts) {
		$this->posts = $posts;
	}

	/**
	 * TODO: zjednodušit, začíná to být moc přeplácané...
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter() {
		define('ITEMCOUNT', $this->posts->countBy());
		$pages = ITEMCOUNT;
		$range = range(1, ceil($pages / 8));
		$paginator = implode('|', $range);

		$router = new RouteList();
		foreach ($this->broken_links as $key => $value) {
			$router[] = new Route($key, $value, Route::ONE_WAY);
		}
		$router[] = new Route('last', array(
			'presenter' => 'Homepage',
			'action' => 'default',
			'paginator-page' => ceil($pages / 8)
		), Route::ONE_WAY);
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
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}