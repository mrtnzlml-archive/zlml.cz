---
id: 71e8347e-9bdb-4d81-a323-203be472f0ad
timestamp: 1376169022000
title: RSS a Sitemap jednoduše a rychle
slug: rss-a-sitemap-jednoduse-a-rychle
---
Pár článků zpět jsem ukazoval několik příkladů, jak tvořit různé routy. Ukazoval jsem routy pro RSS i sitemap.xml. Nikde jsem však zatím neukazoval jak je to jednoduše realizovatelné. Dokonce tak jednoduše, že je škoda tyto soubory nevyužít na jakémkoliv webu, protože mají poměrně velký potenciál.

Začněme HomepagePresenterem (DEV Nette):

```php
<?php

class HomepagePresenter extends BasePresenter {

	/** @var \Model\Posts @inject */
	public $posts;

	public function renderRss() {
		$this->template->posts = $this->posts->getAllPosts()->order('date DESC')->limit(50);
	}

	public function renderSitemap() {
		$this->template->sitemap = $this->posts->getAllPosts();
	}

}
```

Tímto říkám, že do šablon <code>rss.latte</code> a <code>sitemap.latte</code> předávám všechny články, nebo jen některé, protože nechci dělat dump celé databáze pro RSS.

Pro úplnost ještě \Model\Posts:

```php
<?php

namespace Model;

class Posts extends \Nette\Object {

	/** @var \Nette\Database\SelectionFactory @inject */
	public $sf;

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllPosts() {
		return $this->sf->table('posts');
	}

}
```

A následují samotné šablony, které musí dodržovat určitý formát, takže se lehce odlišují od normálních šablon. Sitemap.latte:

```html
{contentType application/xml}
<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	{foreach $sitemap as $s}
		<url>
			<loc>{link //Single:article $s->id}</loc>
		</url>
	{/foreach}
</urlset>
```

Rss.latte:

```html
{contentType application/xml}
<?xml version="1.0" encoding="UTF-8"?>

<rss version="2.0">
	<channel>
		<title>Martin Zlámal [BLOG]</title>
		<link>{link //:Homepage:default}</link>
		<description>Nejnovější články na blogu.</description>
		<language>cs</language>

		<item n:foreach="$posts as $post">
			<title>{$post->title}</title>
			<link>{link //:Single:article $post->id}</link>
			<description>{$post->body|texy|striptags}</description>
		</item>
	</channel>
</rss>
```

A pro úplnou úplnost i router:

```php
<?php

namespace App;
use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;

class RouterFactory {

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter() {
		$router = new RouteList();
		$router[] = new Route('sitemap.xml', 'Homepage:sitemap');
		// na RSS se dá odkazovat normálně bez routeru, nebo:
		$router[] = new Route('rss.xml', 'Homepage:rss');
		//...
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
```

Jednoduché a na pár řádek. Jen vědět jak na to... (-: