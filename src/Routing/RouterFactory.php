<?php declare(strict_types = 1);

namespace App\Routing;

use App\Posts\Posts;
use Nette;
use Nette\Application\Routers\{
	Route,
	RouteList
};

/**
 * Router factory.
 */
class RouterFactory
{

	private $posts;

	private $broken_links = [
		//broken => repaired
		'feed' => 'Homepage:rss',
	];

	public function __construct(Posts $posts)
	{
		$this->posts = $posts;
	}

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		define('ITEMCOUNT', $this->posts->countBy());
		$pages = ITEMCOUNT;
		$range = range(1, ceil($pages / 10));
		$paginator = implode('|', $range);

		$router = new RouteList;
		foreach ($this->broken_links as $key => $value) {
			$router[] = new Route($key, $value, Route::ONE_WAY);
		}
		$router[] = new Route('last', [
			'module' => 'Front',
			'presenter' => 'Homepage',
			'action' => 'default',
			'paginator-page' => ceil($pages / 10),
		], Route::ONE_WAY);
		$router[] = new Route('rss', 'Front:Homepage:rss');
		$router[] = new Route('sitemap.xml', 'Front:Homepage:sitemap');
		$router[] = new Route('auth[/<presenter>/<action>[/<id>]]', [
			'module' => 'Auth',
			'presenter' => 'Sign',
			'action' => 'default',
		]);
		$router[] = new Route('admin[/<action>[/<id>]]', [
			'module' => 'Admin',
			'presenter' => 'Admin',
			'action' => 'default',
		]);
		$router[] = new Route("[<paginator-page [$paginator]>]", [
			'module' => 'Front',
			'presenter' => 'Homepage',
			'action' => 'default',
			'paginator-page' => 1,
		]);
		$router[] = new Route('<slug>', 'Front:Single:article');
		$router[] = new Route('<action>', 'Front:Single:article');
		$router[] = new Route('s[/<search .+>]', 'Front:Search:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Front:Homepage:default');
		return $router;
	}

}
