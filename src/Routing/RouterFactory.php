<?php declare(strict_types = 1);

namespace App\Routing;

use App\Posts\Posts;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nextras\Routing\StaticRouter;

/**
 * Router factory.
 */
class RouterFactory
{

	private $posts;

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

		// Redirect all old urls
		$router[] = new SeoRedirectRouter([
			'Front:Archive:default' => 'obsah',
			'Front:Homepage:default' => [
				'about',
				'reference',
			],
			'Front:Homepage:rss' => 'feed',
			'Front:Single:article,slug=stahnete-si-lepsi-blog' => 'develop',
		]);

		$router[] = new StaticRouter([
			'Front:Homepage:rss' => 'rss',
			'Front:Homepage:sitemap' => 'sitemap.xml',
		]);

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
		$router[] = new Route('archive/<action>', 'Front:Archive:default');
		$router[] = new Route('<slug>', 'Front:Single:article');
		$router[] = new Route('s[/<search .+>]', 'Front:Search:default');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Front:Homepage:default');
		return $router;
	}

}
